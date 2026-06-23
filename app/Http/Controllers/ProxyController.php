<?php

namespace App\Http\Controllers;

use App\Models\ProxyInstance;
use App\Models\Transaction;
use App\Models\User;
use App\Services\CloudSunnyApiService;
use App\Services\CloudSunnyProxyPricingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProxyController extends Controller
{
    public function index()
    {
        $instances = Auth::user()->proxyInstances()->orderBy('id', 'desc')->get();

        return view('proxy.index', compact('instances'));
    }

    public function show(ProxyInstance $proxy, CloudSunnyApiService $api)
    {
        if ($proxy->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $remote = null;
        if ($proxy->provider_proxy_id && $proxy->cloudSunnyAccount) {
            try {
                $remote = $api->forAccount($proxy->cloudSunnyAccount)->getProxy((int) $proxy->provider_proxy_id);
                if ($remote) {
                    $this->syncRemoteProxy($proxy, $remote);
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to fetch proxy status', [
                    'id' => $proxy->id,
                    'provider_proxy_id' => $proxy->provider_proxy_id,
                    'msg' => $e->getMessage(),
                ]);
            }
        }

        return view('proxy.show', [
            'proxy' => $proxy,
            'remote' => $remote,
            'billingCycles' => array_intersect_key(config('cloudsunny.billing_cycles', []), ['monthly' => true]),
        ]);
    }

    public function statusJson(ProxyInstance $proxy, CloudSunnyApiService $api)
    {
        if ($proxy->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        if (!$proxy->provider_proxy_id || !$proxy->cloudSunnyAccount) {
            return response()->json($this->proxyStatusPayload($proxy));
        }

        try {
            $remote = $api->forAccount($proxy->cloudSunnyAccount)->getProxy((int) $proxy->provider_proxy_id);
            if ($remote) {
                $this->syncRemoteProxy($proxy, $remote);
            }

            return response()->json($this->proxyStatusPayload($proxy));
        } catch (\Throwable $e) {
            return response()->json(array_merge($this->proxyStatusPayload($proxy), [
                'error' => $e->getMessage(),
            ]));
        }
    }

    public function renew(Request $request, ProxyInstance $proxy, CloudSunnyApiService $api, CloudSunnyProxyPricingService $pricing)
    {
        if ($proxy->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'billing_cycle' => 'required|in:monthly',
        ]);

        if (!$proxy->provider_proxy_id || !$proxy->cloudSunnyAccount) {
            return back()->with('error', 'Proxy chưa được tạo hoàn chỉnh trên hệ thống.');
        }

        try {
            $data = $api->forAccount($proxy->cloudSunnyAccount)->listProducts();
            $product = collect($data['proxy'] ?? [])->firstWhere('id', $proxy->product_id);

            if (!$product) {
                return back()->with('error', 'Không tìm thấy gói Proxy để tính giá gia hạn.');
            }

            $pricePerCycle = $pricing->priceFor($product, $validated['billing_cycle']);
            $providerCost = $pricing->providerCostFor($product, $validated['billing_cycle']);

            if ($pricePerCycle <= 0) {
                return back()->with('error', 'Chu kỳ thanh toán không được hỗ trợ.');
            }
        } catch (\Throwable $e) {
            return back()->with('error', 'Lỗi lấy thông tin giá: ' . $e->getMessage());
        }

        $user = Auth::user();

        if ($user->balance < $pricePerCycle) {
            return back()->with('error', 'Số dư không đủ để gia hạn.');
        }

        $monthsMeta = config('cloudsunny.billing_cycles.' . $validated['billing_cycle'] . '.months', 1);

        $transaction = null;

        try {
            $transaction = DB::transaction(function () use ($user, $proxy, $pricePerCycle, $providerCost, $validated) {
                $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
                if ($lockedUser->balance < $pricePerCycle) {
                    throw new \RuntimeException('INSUFFICIENT_BALANCE');
                }

                $lockedUser->decrement('balance', $pricePerCycle);

                return Transaction::create([
                    'user_id'           => $lockedUser->id,
                    'proxy_instance_id' => $proxy->id,
                    'type'              => 'renew',
                    'amount'            => $pricePerCycle,
                    'provider_cost'     => $providerCost,
                    'description'       => 'Gia hạn Proxy #' . $proxy->id . ' (' . $validated['billing_cycle'] . ')',
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', 'Số dư không đủ.');
        }

        try {
            $apiClient = $api->forAccount($proxy->cloudSunnyAccount);
            $apiClient->actionProxy([(int) $proxy->provider_proxy_id], 'renew', [
                'billing_cycle' => $validated['billing_cycle'],
            ]);

            $remote = $apiClient->getProxy((int) $proxy->provider_proxy_id);
            if ($remote) {
                $this->syncRemoteProxy($proxy, $remote);
            }

            $baseDate = $proxy->expires_at && $proxy->expires_at->isFuture()
                ? $proxy->expires_at->copy()
                : Carbon::now();

            $expiresAt = $this->remoteProxyExpiresAt($remote ?? []) ?: $baseDate->addMonths($monthsMeta);

            $proxy->update([
                'expires_at' => $expiresAt,
                'paid_amount' => $proxy->paid_amount + $pricePerCycle,
                'billing_cycle' => $validated['billing_cycle'],
            ]);

            return back()->with('success', 'Gia hạn Proxy thành công.');
        } catch (\Throwable $e) {
            User::where('id', $user->id)->increment('balance', $pricePerCycle);
            if ($transaction) {
                $transaction->delete();
            }

            return back()->with('error', 'Gia hạn thất bại, đã hoàn tiền: ' . $e->getMessage());
        }
    }

    public function destroy(ProxyInstance $proxy, CloudSunnyApiService $api)
    {
        if ($proxy->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            if ($proxy->cloudSunnyAccount && $proxy->provider_proxy_id) {
                $api->forAccount($proxy->cloudSunnyAccount)->deleteProxy((int) $proxy->provider_proxy_id);
            }

            DB::transaction(function () use ($proxy) {
                $proxy->update(['status' => 'Đã xoá']);
                $proxy->delete();
            });

            return redirect()->route('proxy.index')->with('success', 'Đã xoá Proxy trên hệ thống.');
        } catch (\Throwable $e) {
            Log::error('Proxy destroy failed', [
                'id' => $proxy->id,
                'provider_proxy_id' => $proxy->provider_proxy_id,
                'msg' => $e->getMessage(),
            ]);

            return back()->with('error', 'Xoá Proxy thất bại: ' . $e->getMessage());
        }
    }

    private function syncRemoteProxy(ProxyInstance $proxy, array $remote): void
    {
        $expiresAt = $this->remoteProxyExpiresAt($remote);

        $proxy->update(array_filter([
            'ip' => $remote['ip'] ?? $proxy->ip,
            'port' => $remote['port'] ?? $proxy->port,
            'username' => $remote['username'] ?? $proxy->username,
            'password' => $remote['password'] ?? $proxy->password,
            'sock5_port' => $remote['sock5_port'] ?? $proxy->sock5_port,
            'sock5_username' => $remote['sock5_username'] ?? $proxy->sock5_username,
            'sock5_password' => $remote['sock5_password'] ?? $proxy->sock5_password,
            'type_proxy' => strtoupper((string) ($remote['type'] ?? $proxy->type_proxy ?? 'HTTP')),
            'status' => $this->mapProxyStatus((string) ($remote['status'] ?? 'on')),
            'expires_at' => $expiresAt,
        ], static function ($value) {
            return $value !== null;
        }));

        $proxy->refresh();
    }

    private function remoteProxyExpiresAt(array $remote): ?Carbon
    {
        foreach (['expires_at', 'expired_at', 'expire_at', 'expiration_at', 'end_at', 'end_date', 'ngay_het_han'] as $key) {
            if (empty($remote[$key])) {
                continue;
            }

            try {
                return Carbon::parse($remote[$key]);
            } catch (\Throwable $e) {
                return null;
            }
        }

        return null;
    }

    private function proxyStatusPayload(ProxyInstance $proxy): array
    {
        return [
            'status' => $proxy->status,
            'ip' => $proxy->ip,
            'port' => $proxy->port,
            'username' => $proxy->username,
            'password' => $proxy->password,
            'sock5_port' => $proxy->sock5_port,
            'sock5_username' => $proxy->sock5_username,
            'sock5_password' => $proxy->sock5_password,
            'type_proxy' => $proxy->type_proxy,
        ];
    }

    private function mapProxyStatus(string $status): string
    {
        $status = strtolower(strip_tags($status));

        if (in_array($status, ['on', 'active', 'running', 'ready', 'hoạt động'], true)) {
            return 'Hoạt động';
        }

        if (in_array($status, ['off', 'inactive', 'stopped', 'tắt', 'đã tắt'], true)) {
            return 'Đã tắt';
        }

        if (in_array($status, ['progressing', 'pending', 'creating', 'waiting'], true)) {
            return 'Đang khởi tạo';
        }

        if (in_array($status, ['renewing', 'renew'], true)) {
            return 'Đang gia hạn';
        }

        if (in_array($status, ['expire', 'expired', 'het han', 'hết hạn'], true)) {
            return 'Hết hạn';
        }

        if (in_array($status, ['cancel', 'cancelled', 'canceled'], true)) {
            return 'Đã huỷ';
        }

        if (in_array($status, ['delete', 'deleted', 'delete_proxy'], true)) {
            return 'Đã xoá';
        }

        return $status ?: 'Đang khởi tạo';
    }
}
