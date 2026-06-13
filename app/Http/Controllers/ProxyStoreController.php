<?php

namespace App\Http\Controllers;

use App\Models\CloudSunnyAccount;
use App\Models\User;
use App\Models\ProxyInstance;
use App\Services\CloudSunnyAccountRouter;
use App\Services\CloudSunnyApiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProxyStoreController extends Controller
{
    public function index(CloudSunnyApiService $api, CloudSunnyAccountRouter $router)
    {
        $account = $router->firstActive();
        $products = [];
        $categories = [];

        if ($account) {
            try {
                $data = $api->forAccount($account)->listProducts();
                $products = $data['proxy'] ?? [];
                $categories = $data['proxy_categories'] ?? [];
            } catch (\Throwable $e) {
                Log::error('Failed to list proxy products: ' . $e->getMessage());
            }
        }

        return view('proxy.store', [
            'products' => $products,
            'categories' => $categories,
            'billingCycles' => config('cloudsunny.billing_cycles', []),
        ]);
    }

    public function store(Request $request, CloudSunnyApiService $api, CloudSunnyAccountRouter $router)
    {
        $cycles = array_keys(config('cloudsunny.billing_cycles', []));

        $validated = $request->validate([
            'product_id' => 'required|integer',
            'billing_cycle' => 'required|in:' . implode(',', $cycles),
            'quantity' => 'required|integer|min:1|max:10',
            'http_username' => 'nullable|string|max:64',
            'http_password' => 'nullable|string|max:64',
            'sock5_username' => 'nullable|string|max:64',
            'sock5_password' => 'nullable|string|max:64',
        ]);

        $account = $router->firstActive();
        if (!$account) {
            return back()->with('error', 'Hệ thống tạm ngừng nhận đơn Proxy. Vui lòng thử lại sau.');
        }

        // To calculate price we need to find the product in the API
        try {
            $data = $api->forAccount($account)->listProducts();
            $products = collect($data['proxy'] ?? []);
            $product = $products->firstWhere('id', $validated['product_id']);

            if (!$product) {
                return back()->with('error', 'Gói Proxy không hợp lệ.');
            }

            $pricePerCycle = $product['data_pricing'][$validated['billing_cycle']] ?? 0;
            
            if ($pricePerCycle <= 0) {
                 return back()->with('error', 'Chu kỳ thanh toán không được hỗ trợ cho gói này.');
            }

            $totalPrice = $pricePerCycle * $validated['quantity'];
        } catch (\Throwable $e) {
             return back()->with('error', 'Lỗi lấy thông tin giá: ' . $e->getMessage());
        }

        $user = Auth::user();

        if ($user->balance < $totalPrice) {
            return back()->withInput()->with('error', 'Số dư không đủ. Vui lòng nạp thêm tiền.');
        }

        $monthsMeta = config('cloudsunny.billing_cycles.'.$validated['billing_cycle'].'.months', 1);
        $expiresAt = Carbon::now()->addMonths($monthsMeta);

        try {
            $proxyIds = DB::transaction(function () use ($user, $account, $validated, $product, $totalPrice, $expiresAt) {
                $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
                if ($lockedUser->balance < $totalPrice) {
                    throw new \RuntimeException('INSUFFICIENT_BALANCE');
                }

                $lockedUser->decrement('balance', $totalPrice);

                $ids = [];
                for ($i = 0; $i < $validated['quantity']; $i++) {
                    $proxy = ProxyInstance::create([
                        'user_id' => $lockedUser->id,
                        'cloudsunny_account_id' => $account->id,
                        'product_id' => $validated['product_id'],
                        'status' => 'Đang khởi tạo...',
                        'billing_cycle' => $validated['billing_cycle'],
                        'paid_amount' => $totalPrice / $validated['quantity'],
                        'expires_at' => $expiresAt,
                        'username' => $validated['http_username'] ?? null,
                        'password' => $validated['http_password'] ?? null,
                        'sock5_username' => $validated['sock5_username'] ?? null,
                        'sock5_password' => $validated['sock5_password'] ?? null,
                    ]);
                    $ids[] = $proxy->id;
                }
                return $ids;
            });
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'INSUFFICIENT_BALANCE') {
                return back()->withInput()->with('error', 'Số dư không đủ.');
            }
            throw $e;
        }

        try {
            $remoteList = $api->forAccount($account)->createProxy([
                'product_id' => (int) $validated['product_id'],
                'billing_cycle' => $validated['billing_cycle'],
                'quantity' => (int) $validated['quantity'],
                'http_username' => $validated['http_username'] ?? '',
                'http_password' => $validated['http_password'] ?? '',
                'http_port' => '',
                'sock5_username' => $validated['sock5_username'] ?? '',
                'sock5_password' => $validated['sock5_password'] ?? '',
                'sock5_port' => '',
            ]);

            // Map remote proxies to local DB instances
            $instances = ProxyInstance::whereIn('id', $proxyIds)->orderBy('id')->get();
            
            foreach ($instances as $index => $proxy) {
                if (isset($remoteList[$index])) {
                    $proxy->update([
                        'provider_proxy_id' => $remoteList[$index]['id'] ?? null,
                        'ip' => $remoteList[$index]['ip'] ?? null,
                        'status' => 'Hoạt động',
                    ]);
                }
            }

            return redirect()->route('proxy.index')->with('success', 'Đã mua thành công ' . $validated['quantity'] . ' Proxy.');

        } catch (\Throwable $e) {
            // Refund
            User::where('id', $user->id)->increment('balance', $totalPrice);
            ProxyInstance::whereIn('id', $proxyIds)->update(['status' => 'Lỗi tạo API']);

            return back()->with('error', 'Lỗi hệ thống khi tạo Proxy. Đã hoàn tiền. (' . $e->getMessage() . ')');
        }
    }
}
