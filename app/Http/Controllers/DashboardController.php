<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VpsInstance;
use App\Services\CloudSunnyApiService;
use App\Services\CloudSunnyPricingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = VpsInstance::with('cloudSunnyAccount')
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('label', 'like', "%{$search}%")
                    ->orWhere('public_ip', 'like', "%{$search}%");
            });
        }

        $instances = $query->paginate(12)->appends($request->all());

        return view('dashboard.index', compact('instances'));
    }

    public function show(VpsInstance $vps, CloudSunnyPricingService $pricing, CloudSunnyApiService $api)
    {
        $this->authorizeVps($vps);

        $plan = $pricing->getPlan($vps->plan_id);
        $renewPrices = [];
        if ($plan) {
            foreach (config('cloudsunny.billing_cycles', []) as $cycle => $meta) {
                $renewPrices[$cycle] = [
                    'label' => $meta['label'],
                    'months' => $meta['months'],
                    'price' => $pricing->calculatePrice($plan, $cycle),
                    'discount_percent' => $meta['discount_percent'] ?? 0,
                ];
            }
        }

        $osList = config('cloudsunny.images', []);
        try {
            $productId = null;
            if ($plan && isset($plan['product_id'])) {
                $productId = (int) $plan['product_id'];
            } elseif ($vps->provider_product_id) {
                $productId = (int) $vps->provider_product_id;
            } else {
                $productId = 1; // Fallback to default VPS product ID to ensure we get the full OS list
            }
            
            if ($productId) {
                $apiOs = $pricing->getImages($productId);
                if (is_array($apiOs) && !empty($apiOs)) {
                    $osList = $apiOs;
                }
            }
        } catch (\Throwable $e) {
            // Fallback to config
        }

        $daysRemaining = $vps->expires_at ? max(1, (int) now()->diffInDays($vps->expires_at, false)) : 30;

        return view('dashboard.show', [
            'vps' => $vps,
            'renewPrices' => $renewPrices,
            'addonPrices' => config('cloudsunny.addon_prices', []),
            'osList' => $osList,
            'daysRemaining' => $daysRemaining,
        ]);
    }

    public function statusJson(VpsInstance $vps, CloudSunnyApiService $api): JsonResponse
    {
        $this->authorizeVps($vps);

        if (!$vps->provider_vps_id || !$vps->cloudSunnyAccount) {
            return response()->json([
                'status' => $vps->status,
                'public_ip' => $vps->public_ip,
                'ready' => false,
            ]);
        }

        try {
            $remote = Cache::remember("vps_status_{$vps->id}", 10, function () use ($api, $vps) {
                return $api->forAccount($vps->cloudSunnyAccount)->getVps((int) $vps->provider_vps_id);
            });
            $this->syncRemoteVps($vps, $remote);

            return response()->json([
                'status' => $vps->status,
                'raw_status' => $remote['status'] ?? null,
                'public_ip' => $vps->public_ip,
                'ready' => $this->isReadyStatus($vps->status),
            ]);
        } catch (\Throwable $e) {
            Log::warning('VPS statusJson failed', ['id' => $vps->id, 'msg' => $e->getMessage()]);

            return response()->json([
                'status' => $vps->status,
                'public_ip' => $vps->public_ip,
                'ready' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function sync(VpsInstance $vps, CloudSunnyApiService $api)
    {
        $this->authorizeVps($vps);
        $this->requireCloudSunnyLink($vps);

        try {
            $remote = $api->forAccount($vps->cloudSunnyAccount)->getVps((int) $vps->provider_vps_id);
            $this->syncRemoteVps($vps, $remote);

            return back()->with('success', 'Đã đồng bộ trạng thái VPS.');
        } catch (\Throwable $e) {
            Log::warning('VPS sync failed', ['id' => $vps->id, 'msg' => $e->getMessage()]);

            return back()->with('error', 'Đồng bộ thất bại: ' . $e->getMessage());
        }
    }

    public function reboot(VpsInstance $vps, CloudSunnyApiService $api)
    {
        $this->authorizeVps($vps);
        $this->requireCloudSunnyLink($vps);

        try {
            $api->forAccount($vps->cloudSunnyAccount)->rebootVps((int) $vps->provider_vps_id);
            $vps->update(['status' => 'Đang khởi động lại']);

            return back()->with('success', 'Đã gửi lệnh reboot VPS.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Reboot thất bại: ' . $e->getMessage());
        }
    }

    public function shutdown(VpsInstance $vps, CloudSunnyApiService $api)
    {
        $this->authorizeVps($vps);
        $this->requireCloudSunnyLink($vps);

        try {
            $api->forAccount($vps->cloudSunnyAccount)->shutdownVps((int) $vps->provider_vps_id);
            $vps->update(['status' => 'Đang tắt']);

            return back()->with('success', 'Đã gửi lệnh tắt nguồn VPS.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Tắt nguồn thất bại: ' . $e->getMessage());
        }
    }

    public function boot(VpsInstance $vps, CloudSunnyApiService $api)
    {
        $this->authorizeVps($vps);
        $this->requireCloudSunnyLink($vps);

        try {
            $api->forAccount($vps->cloudSunnyAccount)->bootVps((int) $vps->provider_vps_id);
            $vps->update(['status' => 'Đang khởi động']);

            return back()->with('success', 'Đã gửi lệnh bật nguồn VPS.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Bật nguồn thất bại: ' . $e->getMessage());
        }
    }

    public function renew(Request $request, VpsInstance $vps, CloudSunnyApiService $api, CloudSunnyPricingService $pricing)
    {
        $this->authorizeVps($vps);
        $this->requireCloudSunnyLink($vps);

        $cycles = array_keys(config('cloudsunny.billing_cycles', []));
        $validated = $request->validate([
            'billing_cycle' => 'required|in:' . implode(',', $cycles),
        ]);

        $plan = $pricing->getPlan($vps->plan_id);
        if (!$plan) {
            return back()->with('error', 'Không tìm thấy gói VPS để tính giá gia hạn.');
        }

        $billingCycle = $validated['billing_cycle'];
        $amount = $pricing->calculatePrice($plan, $billingCycle);
        $providerCost = $pricing->calculateProviderCost($plan, $billingCycle);
        $months = $pricing->monthsForCycle($billingCycle);
        $user = Auth::user();

        if ($user->balance < $amount) {
            return back()->with('error', 'Số dư không đủ để gia hạn VPS.');
        }

        try {
            DB::transaction(function () use ($user, $amount) {
                $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
                if ($lockedUser->balance < $amount) {
                    throw new \RuntimeException('INSUFFICIENT_BALANCE');
                }

                $lockedUser->decrement('balance', $amount);
            });

            $api->forAccount($vps->cloudSunnyAccount)->renewVps((int) $vps->provider_vps_id, $billingCycle);

            $baseDate = $vps->expires_at && $vps->expires_at->isFuture()
                ? $vps->expires_at->copy()
                : Carbon::now();

            $vps->update([
                'billing_cycle' => $billingCycle,
                'paid_amount' => (int) $vps->paid_amount + $amount,
                'provider_cost' => (int) $vps->provider_cost + $providerCost,
                'expires_at' => $baseDate->addMonths($months),
                'status' => $vps->status === 'Hết hạn' ? 'Sẵn sàng' : $vps->status,
            ]);

            return back()->with('success', 'Đã gia hạn VPS thêm ' . $months . ' tháng.');
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'INSUFFICIENT_BALANCE') {
                return back()->with('error', 'Số dư không đủ.');
            }

            return back()->with('error', 'Gia hạn thất bại: ' . $e->getMessage());
        } catch (\Throwable $e) {
            User::where('id', $user->id)->increment('balance', $amount);

            return back()->with('error', 'Gia hạn thất bại, đã hoàn tiền: ' . $e->getMessage());
        }
    }

    public function upgrade(Request $request, VpsInstance $vps, CloudSunnyApiService $api)
    {
        $this->authorizeVps($vps);
        $this->requireCloudSunnyLink($vps);

        $validated = $request->validate([
            'addon_cpu' => 'nullable|integer|min:0|max:16',
            'addon_ram' => 'nullable|integer|min:0|max:64',
            'addon_disk' => 'nullable|integer|min:0|max:1000',
        ]);

        $addonCpu = (int) ($validated['addon_cpu'] ?? 0);
        $addonRam = (int) ($validated['addon_ram'] ?? 0);
        $addonDisk = (int) ($validated['addon_disk'] ?? 0);

        if ($addonCpu < 1 && $addonRam < 1 && $addonDisk < 1) {
            return back()->with('error', 'Chọn CPU, RAM hoặc Disk cần nâng cấp.');
        }

        if ($addonDisk > 0 && $addonDisk % 10 !== 0) {
            return back()->with('error', 'Disk nâng cấp phải là bội số của 10GB.');
        }

        $amount = $this->calcAddonPrice($vps, $addonCpu, $addonRam, $addonDisk);
        $user = Auth::user();

        if ($user->balance < $amount) {
            return back()->with('error', 'Số dư không đủ để nâng cấp VPS.');
        }

        try {
            DB::transaction(function () use ($user, $amount) {
                $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
                if ($lockedUser->balance < $amount) {
                    throw new \RuntimeException('INSUFFICIENT_BALANCE');
                }

                $lockedUser->decrement('balance', $amount);
            });

            $api->forAccount($vps->cloudSunnyAccount)->upgradeVps((int) $vps->provider_vps_id, $addonCpu, $addonRam, $addonDisk);

            $vps->update([
                'cpu' => (int) $vps->cpu + $addonCpu,
                'ram' => (int) $vps->ram + $addonRam,
                'disk' => (int) $vps->disk + $addonDisk,
                'paid_amount' => (int) $vps->paid_amount + $amount,
                'status' => 'Đang khởi động lại',
            ]);

            return back()->with('success', 'Đã gửi yêu cầu nâng cấp VPS.');
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'INSUFFICIENT_BALANCE') {
                return back()->with('error', 'Số dư không đủ.');
            }

            return back()->with('error', 'Nâng cấp thất bại: ' . $e->getMessage());
        } catch (\Throwable $e) {
            User::where('id', $user->id)->increment('balance', $amount);

            return back()->with('error', 'Nâng cấp thất bại, đã hoàn tiền: ' . $e->getMessage());
        }
    }

    public function changePassword(Request $request, VpsInstance $vps)
    {
        $this->authorizeVps($vps);

        return back()->with('error', 'SeaServer API docs hiện tại chưa có endpoint đổi mật khẩu VPS.');
    }

    public function rebuild(Request $request, VpsInstance $vps, CloudSunnyApiService $api)
    {
        $this->authorizeVps($vps);
        $this->requireCloudSunnyLink($vps);

        $request->validate([
            'os_id' => 'required|integer',
        ]);

        try {
            $api->forAccount($vps->cloudSunnyAccount)->rebuildVps((int) $vps->provider_vps_id, (int) $request->os_id);
            
            $vps->update([
                'status' => 'Đang khởi động lại',
            ]);

            return back()->with('success', 'Đã gửi yêu cầu Rebuild OS. VPS sẽ bị xóa hết dữ liệu và cài đặt lại!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Rebuild thất bại: ' . $e->getMessage());
        }
    }

    public function destroy(VpsInstance $vps, CloudSunnyApiService $api)
    {
        $this->authorizeVps($vps);

        try {
            if ($vps->cloudSunnyAccount && $vps->provider_vps_id) {
                // Call API to delete on the provider side
                $api->forAccount($vps->cloudSunnyAccount)->deleteVps((int) $vps->provider_vps_id);
            }

            DB::transaction(function () use ($vps) {
                $vps->update(['status' => 'Đã xóa']);
                $vps->delete();
            });

            return redirect()->route('dashboard')->with('success', 'Đã xóa VPS trên hệ thống.');
        } catch (\Throwable $e) {
            Log::error('VPS destroy failed', ['id' => $vps->id, 'msg' => $e->getMessage()]);

            return back()->with('error', 'Xóa VPS thất bại: ' . $e->getMessage());
        }
    }

    private function authorizeVps(VpsInstance $vps): void
    {
        $user = Auth::user();
        if ($vps->user_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }
    }

    private function requireCloudSunnyLink(VpsInstance $vps): void
    {
        if (!$vps->cloudSunnyAccount || !$vps->provider_vps_id) {
            abort(400, 'VPS chưa liên kết SeaServer API.');
        }
    }

    private function syncRemoteVps(VpsInstance $vps, array $remote): void
    {
        $vps->update([
            'public_ip' => $this->remoteValue($remote, ['ip', 'ip_address', 'ipv4', 'main_ip', 'public_ip'], $vps->public_ip),
            'login_username' => $this->remoteValue($remote, ['username', 'user', 'login_username', 'login_user'], $vps->login_username),
            'root_password' => $this->remoteValue($remote, ['password', 'root_password', 'login_password'], $vps->root_password),
            'status' => $this->mapStatus((string) $this->remoteValue($remote, ['status', 'state', 'power_status', 'vm_status', 'trang_thai'], $vps->status)),
            'provider_payload' => $remote,
        ]);
    }

    private function calcAddonPrice(VpsInstance $vps, int $addonCpu, int $addonRam, int $addonDisk): int
    {
        $days = $vps->expires_at ? max(1, (int) now()->diffInDays($vps->expires_at, false)) : 30;
        $prices = config('cloudsunny.addon_prices', []);

        $monthly = 0;
        $monthly += $addonCpu * (int) ($prices['cpu_monthly'] ?? 0);
        $monthly += $addonRam * (int) ($prices['ram_monthly'] ?? 0);
        $monthly += (int) ($addonDisk / 10) * (int) ($prices['disk_10gb_monthly'] ?? 0);

        $fullMonths = floor($days / 30);
        $remainderDays = $days % 30;
        
        $chargeFactor = $fullMonths;
        if ($remainderDays > 15) {
            $chargeFactor += 1;
        } elseif ($remainderDays > 0) {
            $chargeFactor += 0.5;
        }

        return max(0, (int) round($monthly * $chargeFactor));
    }

    private function mapStatus(string $status): string
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

    private function isReadyStatus(string $status): bool
    {
        return in_array($status, ['Sẵn sàng', 'Đang chạy'], true);
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
