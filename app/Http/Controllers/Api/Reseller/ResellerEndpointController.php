<?php

namespace App\Http\Controllers\Api\Reseller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VpsInstance;
use App\Models\Transaction;
use App\Services\CloudSunnyPricingService;
use App\Services\CloudSunnyApiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ResellerEndpointController extends Controller
{
    protected $pricingService;
    protected $apiService;

    public function __construct(CloudSunnyPricingService $pricingService, CloudSunnyApiService $apiService)
    {
        $this->pricingService = $pricingService;
        $this->apiService = $apiService;
    }

    /**
     * API: Thông tin Tài khoản
     * GET /api/v1/profile
     */
    public function profile()
    {
        $user = Auth::user();
        
        $vpsActive = VpsInstance::where('user_id', $user->id)
            ->get()
            ->filter(fn($v) => $v->isActive())
            ->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'email' => $user->email,
                'balance' => $user->balance,
                'vps_active' => $vpsActive,
                'created_at' => $user->created_at->toIso8601String()
            ]
        ]);
    }

    /**
     * API: Lấy Danh mục và Bảng giá
     * GET /api/v1/plans
     */
    public function plans()
    {
        // Lấy danh sách plans từ Service
        $plansData = $this->pricingService->getPlans();
        
        // Lấy danh sách OS từ Service. Do cấu trúc yêu cầu productId, ta lấy ID của Plan đầu tiên
        $firstPlan = reset($plansData);
        $firstProductId = $firstPlan ? ($firstPlan['product_id'] ?? 1) : 1;
        $imagesData = $this->pricingService->getImages($firstProductId);

        $osList = [];
        foreach ($imagesData as $osId => $os) {
            $osList[] = [
                'id' => $osId,
                'name' => $os['label'] ?? null,
                'type' => strtolower($os['group'] ?? 'linux'),
            ];
        }

        $plans = [];
        foreach ($plansData as $slug => $plan) {
            $plans[] = [
                'id' => $plan['product_id'] ?? $slug,
                'name' => $plan['name'] ?? null,
                'cpu' => $plan['cores'] ?? 1,
                'ram' => $plan['ram'] ?? 1024,
                'disk' => $plan['disk'] ?? 25,
                'price_monthly' => $plan['price_per_month'] ?? 0,
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'location' => 'Vietnam (VN)',
                'os_list' => $osList,
                'plans' => $plans
            ]
        ]);
    }

    /**
     * API: Tạo VPS (Mua thật)
     * POST /api/v1/vps/create
     */
    public function createVps(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|integer',
            'os_id' => 'required|integer',
            'duration' => 'required|integer|in:1,3,6,12',
            'hostname' => 'nullable|string|max:255'
        ]);

        $user = Auth::user();
        $planId = $request->plan_id;
        $osId = $request->os_id;
        $duration = $request->duration;
        $hostname = $request->hostname ?? 'server-' . time();

        // 1. Kiểm tra plan_id và tính toán giá tiền từ PricingService
        $plansData = $this->pricingService->getPlans();
        
        $selectedPlan = null;
        foreach ($plansData as $slug => $plan) {
            if (($plan['product_id'] ?? $slug) == $planId) {
                $selectedPlan = $plan;
                break;
            }
        }
        
        if (!$selectedPlan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gói cấu hình không hợp lệ.'
            ], 400);
        }

        $totalPrice = ($selectedPlan['price_per_month'] ?? 0) * $duration;

        // 2. Kiểm tra số dư Đại lý
        if ($user->balance < $totalPrice) {
            return response()->json([
                'status' => 'error',
                'message' => 'Số dư không đủ. Vui lòng nạp thêm tiền.'
            ], 402);
        }

        // 3. Trừ tiền và tạo giao dịch
        $user->balance -= $totalPrice;
        $user->save();

        // Lưu vào bảng Transaction
        Transaction::create([
            'user_id' => $user->id,
            'amount' => -$totalPrice,
            'type' => 'buy_vps',
            'status' => 'success',
            'description' => "Đại lý mua gói {$selectedPlan['name']} (x{$duration} tháng) qua API",
            'balance_after' => $user->balance,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Tạo bản ghi VPS ở trạng thái Processing
        // Cột provider_order_id trong DB được thiết kế là số nguyên (Integer)
        // Nên ta sinh ra một mã số Order toàn số
        $orderId = time() . random_int(100, 999);
        $vps = VpsInstance::create([
            'user_id' => $user->id,
            'label' => $hostname,
            'plan_id' => $planId,
            'status' => 'Đang xử lý', // processing
            'provider_os_id' => $osId,
            'billing_cycle' => $duration . ' tháng',
            'expires_at' => now()->addMonths($duration),
            'provider_order_id' => $orderId,
            'root_password' => '...',
            'login_username' => 'root',
            'region' => 'VN',
            'cost_monthly_usd' => 0,
            // (Thêm các trường cần thiết khác)
        ]);

        // 5. Đẩy tiến trình gọi sang nhà cung cấp Gốc qua Queue (Background Job)
        // Chúng ta sẽ tạo một Job ProcessResellerVpsCreation.
        \App\Jobs\ProcessResellerVpsCreation::dispatch($vps->id, $user->id, $planId, $osId);

        // 6. Trả về Response
        return response()->json([
            'status' => 202,
            'message' => 'VPS đang được khởi tạo. Hệ thống sẽ tự động gọi Webhook khi hoàn tất.',
            'data' => [
                'order_id' => $orderId,
                'amount_deducted' => $totalPrice,
                'balance_left' => $user->balance,
                'status' => 'processing',
                'status_url' => url("/api/v1/vps/{$orderId}/status")
            ]
        ], 202);
    }

    /**
     * API: Get Status
     * GET /api/v1/vps/{order_id}/status
     */
    public function vpsStatus($orderId)
    {
        $user = Auth::user();
        
        $vps = VpsInstance::where('user_id', $user->id)
            ->where('provider_order_id', $orderId)
            ->first();

        if (!$vps) {
            return response()->json([
                'status' => 404,
                'message' => 'Order ID không tồn tại hoặc không thuộc quyền sở hữu của bạn.'
            ], 404);
        }

        // Map status về chuẩn tiếng anh để đại lý dễ dùng
        $apiStatus = 'processing';
        if ($vps->isActive() && $vps->public_ip) {
            $apiStatus = 'active';
        } elseif (str_contains(strtolower($vps->status), 'lỗi')) {
            $apiStatus = 'failed';
        }

        $vpsInfo = null;
        if ($apiStatus === 'active') {
            $vpsInfo = [
                'ip_address' => $vps->public_ip,
                'username' => $vps->login_username ?? 'root',
                'password' => $vps->root_password, // Chú ý: Cột này được cấu hình Encrypted, tự động decrypt
                'os_name' => $vps->provider_payload['os_name'] ?? 'Linux',
                'created_at' => $vps->created_at->toIso8601String(),
                'expired_at' => $vps->expires_at ? $vps->expires_at->toIso8601String() : null,
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'order_id' => $orderId,
                'status' => $apiStatus,
                'vps_info' => $vpsInfo
            ]
        ]);
    }
}
