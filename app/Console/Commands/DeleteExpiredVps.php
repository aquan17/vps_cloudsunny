<?php

namespace App\Console\Commands;

use App\Models\VpsInstance;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DeleteExpiredVps extends Command
{
    protected $signature = 'vps:delete-expired';
    protected $description = 'Danh dau VPS và Proxy da qua han tren he thong.';

    public function handle(): int
    {
        $expiredVpsList = VpsInstance::where('expires_at', '<=', Carbon::now()->subMinutes(10))
            ->where('status', '!=', 'Đã xóa')
            ->get();

        foreach ($expiredVpsList as $vps) {
            try {
                $vps->update(['status' => 'Hết hạn']);
                $this->info("Expired VPS ID {$vps->id}");
            } catch (\Throwable $e) {
                Log::error('Cron expire VPS failed', ['vps_id' => $vps->id, 'msg' => $e->getMessage()]);
                $this->error("VPS {$vps->id}: {$e->getMessage()}");
            }
        }

        // Expire Proxies
        $expiredProxies = \App\Models\ProxyInstance::where('expires_at', '<=', Carbon::now()->subMinutes(10))
            ->where('status', '!=', 'Hết hạn')
            ->where('status', '!=', 'Đã xóa')
            ->get();

        foreach ($expiredProxies as $proxy) {
            try {
                $proxy->update(['status' => 'Hết hạn']);
                $this->info("Expired Proxy ID {$proxy->id}");
            } catch (\Throwable $e) {
                Log::error('Cron expire Proxy failed', ['proxy_id' => $proxy->id, 'msg' => $e->getMessage()]);
                $this->error("Proxy {$proxy->id}: {$e->getMessage()}");
            }
        }

        return 0;
    }
}
