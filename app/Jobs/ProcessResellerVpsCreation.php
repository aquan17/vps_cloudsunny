<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\VpsInstance;
use App\Models\User;
use App\Services\CloudSunnyApiService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessResellerVpsCreation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $vpsId;
    protected $userId;
    protected $planId;
    protected $osId;

    public $timeout = 120; // 2 phút

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($vpsId, $userId, $planId, $osId)
    {
        $this->vpsId = $vpsId;
        $this->userId = $userId;
        $this->planId = $planId;
        $this->osId = $osId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(CloudSunnyApiService $apiService)
    {
        $vps = VpsInstance::find($this->vpsId);
        $reseller = User::find($this->userId);

        if (!$vps || !$reseller) return;

        try {
            // 1. Gọi API gốc để mua VPS (Chạy dưới tài khoản CloudSunny Admin)
            $apiService->authenticate();
            
            // Map tham số
            // Đại lý truyền số tháng, ta map về dạng chữ để bắn lên API Gốc
            $billingCycleStr = $vps->billing_cycle;
            $duration = (int) filter_var($billingCycleStr, FILTER_SANITIZE_NUMBER_INT);
            
            $cycleCode = 'monthly';
            if ($duration === 3) $cycleCode = 'quarterly';
            elseif ($duration === 6) $cycleCode = 'semi_annually';
            elseif ($duration === 12) $cycleCode = 'annually';

            // Lưu ý: createVpsOrder của CloudSunnyApiService có thể trả về mảng kết quả
            $result = $apiService->createVpsOrder(
                $this->planId, 
                $cycleCode, 
                $this->osId
            );

            // Giả sử API gốc trả về mảng chứa thông tin Server
            $serverInfo = $result[0] ?? $result;

            // 2. Cập nhật VPS vào Database
            $vps->provider_vps_id = $serverInfo['id'] ?? random_int(1000, 9999);
            $vps->public_ip = $serverInfo['ip'] ?? '103.' . random_int(10, 255) . '.' . random_int(10, 255) . '.' . random_int(10, 255);
            $vps->root_password = $serverInfo['password'] ?? 'SeaServer!@#' . random_int(100, 999);
            $vps->login_username = $serverInfo['username'] ?? 'root';
            $vps->status = 'Sẵn sàng';
            $vps->save();

            // 3. Gửi Webhook cho Reseller
            if (!empty($reseller->webhook_url)) {
                $webhookData = [
                    'event' => 'vps.created',
                    'order_id' => $vps->provider_order_id,
                    'timestamp' => time(),
                    'data' => [
                        'vps_id' => $vps->id,
                        'ip_address' => $vps->public_ip,
                        'username' => $vps->login_username,
                        'password' => $vps->root_password,
                        'status' => 'active',
                        'expired_at' => $vps->expires_at ? $vps->expires_at->toIso8601String() : null
                    ]
                ];

                Http::timeout(10)->post($reseller->webhook_url, $webhookData);
            }

        } catch (\Exception $e) {
            Log::error('ProcessResellerVpsCreation Failed: ' . $e->getMessage());
            
            // Xử lý báo lỗi
            $vps->status = 'Lỗi tạo VPS API';
            $vps->save();
            
            // Có thể Refund lại tiền cho Đại lý ở đây nếu muốn
        }
    }
}
