<?php

namespace App\Http\Controllers;

use App\Mail\VpsCreated;
use App\Models\CloudSunnyAccount;
use App\Models\User;
use App\Models\VpsInstance;
use App\Services\CloudSunnyAccountRouter;
use App\Services\CloudSunnyApiService;
use App\Services\CloudSunnyPricingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class StoreController extends Controller
{
    public function index(CloudSunnyPricingService $pricing)
    {
        $plans = $pricing->getPlans();
        $hasProvider = CloudSunnyAccount::where('is_active', true)->where('is_full', false)->exists()
            || (bool) config('cloudsunny.api_username');

        return view('store.index', [
            'plans' => $plans,
            'regions' => $pricing->getRegions(),
            'durations' => $pricing->getDurations(),
            'availablePlanIds' => $hasProvider ? array_keys($plans) : [],
            'maxAvailableUsd' => 0,
        ]);
    }

    public function create(string $plan, CloudSunnyPricingService $pricing)
    {
        $planData = $pricing->getPlan($plan);
        if (!$planData) {
            abort(404);
        }

        return view('store.create', [
            'planId' => $plan,
            'plan' => $planData,
            'regions' => $pricing->getRegions(),
            'durations' => $pricing->getDurations(),
            'images' => $pricing->getImages((int) $planData['product_id']),
        ]);
    }

    public function store(
        Request $request,
        CloudSunnyPricingService $pricing,
        CloudSunnyAccountRouter $router,
        CloudSunnyApiService $api
    ) {
        $plans = array_keys($pricing->getPlans());
        $cycles = array_keys(config('cloudsunny.billing_cycles', []));

        $validated = $request->validate([
            'plan' => 'required|in:' . implode(',', $plans),
            'region' => 'nullable|string|max:32',
            'duration' => 'required|in:' . implode(',', $cycles),
            'label' => 'required|string|min:3|max:32|regex:/^[a-zA-Z0-9\-]+$/',
            'image' => 'required|integer|min:1',
            'addon_cpu' => 'nullable|integer|min:0|max:16',
            'addon_ram' => 'nullable|integer|min:0|max:64',
            'addon_disk' => 'nullable|integer|min:0|max:1000',
        ]);

        $plan = $pricing->getPlan($validated['plan']);
        $billingCycle = $validated['duration'];
        $months = $pricing->monthsForCycle($billingCycle);
        $addonCpu = (int) ($validated['addon_cpu'] ?? 0);
        $addonRam = (int) ($validated['addon_ram'] ?? 0);
        $addonDisk = (int) ($validated['addon_disk'] ?? 0);

        if ($addonDisk > 0 && $addonDisk % 10 !== 0) {
            return back()->withInput()->with('error', 'Disk nâng cấp phải là bội số của 10GB.');
        }

        $totalPrice = $pricing->calculatePrice($plan, $billingCycle)
            + $this->calcAddonPrice($months, $addonCpu, $addonRam, $addonDisk);
        $user = Auth::user();

        if ($user->balance < $totalPrice) {
            return back()->withInput()->with('error', 'Số dư không đủ. Vui long nap them tien.');
        }

        $account = $router->pickForOrder($totalPrice);
        if (!$account) {
            return back()->withInput()->with('error', 'Tạm hết slot trên hệ thống. Vui lòng thử lại sau.');
        }

        $expiresAt = Carbon::now()->addMonths($months);

        try {
            $providerCost = $pricing->calculateProviderCost($plan, $billingCycle);
            // Addon costs are not strictly tracked from API, so we just use the base provider cost for now.

            $vps = DB::transaction(function () use ($user, $account, $validated, $plan, $totalPrice, $providerCost, $expiresAt, $billingCycle, $addonCpu, $addonRam, $addonDisk) {
                $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
                if ($lockedUser->balance < $totalPrice) {
                    throw new \RuntimeException('INSUFFICIENT_BALANCE');
                }

                $lockedUser->decrement('balance', $totalPrice);

                return VpsInstance::create([
                    'user_id' => $lockedUser->id,
                    'cloudsunny_account_id' => $account->id,
                    'label' => $validated['label'],
                    'root_password' => '',
                    'region' => $validated['region'] ?? 'vn',
                    'plan_id' => $validated['plan'],
                    'status' => 'Đang khởi tạo...',
                    'cpu' => $plan['cores'] + $addonCpu,
                    'ram' => $plan['ram'] + $addonRam,
                    'disk' => $plan['disk'] + $addonDisk,
                    'cost_monthly_usd' => 0,
                    'provider_cost' => $providerCost,
                    'provider_product_id' => (int) $plan['product_id'],
                    'provider_os_id' => (int) $validated['image'],
                    'billing_cycle' => $billingCycle,
                    'paid_amount' => $totalPrice,
                    'expires_at' => $expiresAt,
                ]);
            });
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'INSUFFICIENT_BALANCE') {
                return back()->withInput()->with('error', 'Số dư không đủ.');
            }
            throw $e;
        }

        try {
            $remoteList = $api->forAccount($account)->createVpsOrder(
                (int) $plan['product_id'],
                $billingCycle,
                (int) $validated['image'],
                1,
                $addonCpu,
                $addonRam,
                $addonDisk
            );

            $remote = $remoteList[0] ?? $remoteList;

            $vps->update([
                'provider_vps_id' => $this->remoteValue($remote, ['id', 'vps_id', 'server_id', 'service_id'], null),
                'provider_order_id' => $this->remoteValue($remote, ['order_id', 'orderId'], null),
                'public_ip' => $this->remoteValue($remote, ['ip', 'ip_address', 'ipv4', 'main_ip', 'public_ip'], null),
                'login_username' => $this->remoteValue($remote, ['username', 'user', 'login_username', 'login_user'], null),
                'root_password' => $this->remoteValue($remote, ['password', 'root_password', 'login_password'], ''),
                'status' => $this->mapCloudSunnyStatus((string) $this->remoteValue($remote, ['status', 'state', 'power_status', 'vm_status', 'trang_thai'], 'progressing')),
                'provider_payload' => $remote,
            ]);
        } catch (\Throwable $e) {
            Log::error('CloudSunny create failed', ['vps_id' => $vps->id, 'msg' => $e->getMessage()]);

            DB::transaction(function () use ($user, $totalPrice, $vps) {
                User::where('id', $user->id)->increment('balance', $totalPrice);
                $vps->update(['status' => 'Lỗi - đã hoàn tiền']);
            });

            return redirect()->route('dashboard')->with('error', 'Tạo VPS thất bại, đã hoàn tiền: ' . $e->getMessage());
        }

        try {
            Mail::to($user->email)->queue(new VpsCreated($vps, (string) $vps->root_password));
        } catch (\Throwable $e) {
            Log::error('Failed to send VPS created email', ['vps_id' => $vps->id, 'msg' => $e->getMessage()]);
        }

        return redirect()->route('dashboard.show', $vps)->with('success', 'VPS đã được tạo thành công. Thông tin đăng nhập sẽ được cập nhật khi VPS sẵn sàng.');
    }

    private function mapCloudSunnyStatus(string $status): string
    {
        $status = $this->normalizeStatus($status);

        if ($this->containsAny($status, ['running', 'online', 'ready', 'started', 'power on', 'poweron', 'on', 'completed', 'complete', 'đang chạy', 'hoat dong', 'sẵn sàng'])) {
            return 'Sẵn sàng';
        }
        if ($this->containsAny($status, ['offline', 'stopped', 'shutdown', 'power off', 'poweroff', 'đã tắt'])) {
            return 'Đã tắt';
        }
        if ($this->containsAny($status, ['deleted', 'removed', 'đã xóa'])) {
            return 'Đã xóa';
        }
        if ($this->containsAny($status, ['error', 'failed', 'fail', 'loi'])) {
            return 'Lỗi';
        }

        if ($this->containsAny($status, ['active', 'success', 'bat', 'bật'])) {
            return 'Sẵn sàng';
        }
        if ($this->containsAny($status, ['off', 'tat', 'tắt'])) {
            return 'Đã tắt';
        }
        if ($this->containsAny($status, ['expired', 'het', 'hết'])) {
            return 'Hết hạn';
        }
        if ($this->containsAny($status, ['cancel'])) {
            return 'Đã xóa';
        }

        return 'Đang khởi tạo...';
    }

    private function calcAddonPrice(int $months, int $addonCpu, int $addonRam, int $addonDisk): int
    {
        $prices = config('cloudsunny.addon_prices', []);
        $monthly = 0;
        $monthly += $addonCpu * (int) ($prices['cpu_monthly'] ?? 0);
        $monthly += $addonRam * (int) ($prices['ram_monthly'] ?? 0);
        $monthly += (int) ($addonDisk / 10) * (int) ($prices['disk_10gb_monthly'] ?? 0);

        return max(0, $monthly * max(1, $months));
    }

    private function normalizeStatus(string $status): string
    {
        $status = strtolower(strip_tags($status));
        $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $status);
        if (is_string($ascii) && $ascii !== '') {
            $status = $ascii;
        }

        return preg_replace('/[^a-z0-9]+/', ' ', $status) ?: '';
    }

    private function containsAny(string $value, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (strpos($value, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    private function remoteValue(array $remote, array $keys, $default = null)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $remote) && $remote[$key] !== null && $remote[$key] !== '') {
                return $remote[$key];
            }
        }

        return $default;
    }
}
