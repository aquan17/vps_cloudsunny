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
        if ($proxy->user_id !== Auth::id()) {
            abort(403);
        }

        $remote = null;
        if ($proxy->provider_proxy_id && $proxy->cloudSunnyAccount) {
            try {
                $remote = $api->forAccount($proxy->cloudSunnyAccount)->getProxy($proxy->provider_proxy_id);
                // Sync data
                if ($remote) {
                    $proxy->update([
                        'ip' => $remote['ip'] ?? $proxy->ip,
                        'port' => $remote['port'] ?? $proxy->port,
                        'username' => $remote['username'] ?? $proxy->username,
                        'password' => $remote['password'] ?? $proxy->password,
                        'sock5_port' => $remote['sock5_port'] ?? $proxy->sock5_port,
                        'sock5_username' => $remote['sock5_username'] ?? $proxy->sock5_username,
                        'sock5_password' => $remote['sock5_password'] ?? $proxy->sock5_password,
                        'status' => 'Hoạt động',
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to fetch proxy status', ['id' => $proxy->id, 'msg' => $e->getMessage()]);
            }
        }

        return view('proxy.show', [
            'proxy' => $proxy,
            'remote' => $remote,
            'billingCycles' => config('cloudsunny.billing_cycles', []),
        ]);
    }

    public function statusJson(ProxyInstance $proxy, CloudSunnyApiService $api)
    {
        if ($proxy->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$proxy->provider_proxy_id || !$proxy->cloudSunnyAccount) {
            return response()->json([
                'status' => $proxy->status,
                'ip' => $proxy->ip,
            ]);
        }

        try {
            $remote = $api->forAccount($proxy->cloudSunnyAccount)->getProxy($proxy->provider_proxy_id);
            if ($remote) {
                $proxy->update([
                    'ip' => $remote['ip'] ?? $proxy->ip,
                    'status' => 'Hoạt động',
                ]);
            }
            return response()->json([
                'status' => $proxy->status,
                'ip' => $proxy->ip,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => $proxy->status,
                'ip' => $proxy->ip,
                'error' => $e->getMessage(),
            ]);
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

        // We need to fetch product to calculate renew price
        try {
            $data = $api->forAccount($proxy->cloudSunnyAccount)->listProducts();
            $products = collect($data['proxy'] ?? []);
            $product = $products->firstWhere('id', $proxy->product_id);

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

        $monthsMeta = config('cloudsunny.billing_cycles.'.$validated['billing_cycle'].'.months', 1);

        try {
            DB::transaction(function () use ($user, $proxy, $pricePerCycle, $providerCost, $validated) {
                $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
                if ($lockedUser->balance < $pricePerCycle) {
                    throw new \RuntimeException('INSUFFICIENT_BALANCE');
                }
                $lockedUser->decrement('balance', $pricePerCycle);

                Transaction::create([
                    'user_id' => $lockedUser->id,
                    'type' => 'renew',
                    'amount' => $pricePerCycle,
                    'provider_cost' => $providerCost,
                    'description' => 'Gia han Proxy #' . $proxy->id . ' (' . $validated['billing_cycle'] . ')',
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', 'Số dư không đủ.');
        }

        try {
            $api->forAccount($proxy->cloudSunnyAccount)->actionProxy([$proxy->provider_proxy_id], 'renew', [
                'billing_cycle' => $validated['billing_cycle']
            ]);

            $expiresAt = $proxy->expires_at 
                ? Carbon::parse($proxy->expires_at)->addMonths($monthsMeta) 
                : Carbon::now()->addMonths($monthsMeta);

            $proxy->update([
                'expires_at' => $expiresAt,
                'paid_amount' => $proxy->paid_amount + $pricePerCycle,
                'billing_cycle' => $validated['billing_cycle'],
            ]);

            return back()->with('success', 'Gia hạn Proxy thành công.');

        } catch (\Throwable $e) {
            User::where('id', $user->id)->increment('balance', $pricePerCycle);
            return back()->with('error', 'Gia hạn thất bại, đã hoàn tiền: ' . $e->getMessage());
        }
    }
}
