<?php

namespace App\Console\Commands;

use App\Mail\VpsExpiring;
use App\Models\VpsInstance;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckExpiringVps extends Command
{
    protected $signature = 'cloudsunny:check-expiring';
    protected $description = 'Gui email canh bao VPS và Proxy sap hết hạn.';

    public function handle(): int
    {
        $targetDate = now()->addDays(3)->toDateString();

        $instances = VpsInstance::whereIn('status', ['Sẵn sàng', 'Đang chạy', 'Đã tắt'])
            ->whereDate('expires_at', $targetDate)
            ->with('user')
            ->get();

        $countVps = 0;
        foreach ($instances as $vps) {
            if (!$vps->user || !$vps->user->email) {
                continue;
            }

            try {
                Mail::to($vps->user->email)->send(new VpsExpiring($vps, 3));
                $countVps++;
            } catch (\Throwable $e) {
                Log::error('Failed to send VPS expiration email', [
                    'vps_id' => $vps->id,
                    'msg' => $e->getMessage(),
                ]);
            }
        }

        // Check Proxies
        $proxies = \App\Models\ProxyInstance::whereIn('status', ['Hoạt động'])
            ->whereDate('expires_at', $targetDate)
            ->with('user')
            ->get();

        $countProxy = 0;
        foreach ($proxies as $proxy) {
            if (!$proxy->user || !$proxy->user->email) {
                continue;
            }

            try {
                Mail::to($proxy->user->email)->send(new \App\Mail\ProxyExpiring($proxy, 3));
                $countProxy++;
            } catch (\Throwable $e) {
                Log::error('Failed to send Proxy expiration email', [
                    'proxy_id' => $proxy->id,
                    'msg' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Completed. Sent {$countVps} VPS and {$countProxy} Proxy warning emails.");

        return 0;
    }
}
