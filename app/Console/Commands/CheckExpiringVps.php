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
    protected $description = 'Gui email canh bao VPS sap hết hạn.';

    public function handle(): int
    {
        $targetDate = now()->addDays(3)->toDateString();

        $instances = VpsInstance::whereIn('status', ['Sẵn sàng', 'Đang chạy', 'Đã tắt'])
            ->whereDate('expires_at', $targetDate)
            ->with('user')
            ->get();

        $count = 0;
        foreach ($instances as $vps) {
            if (!$vps->user || !$vps->user->email) {
                continue;
            }

            try {
                Mail::to($vps->user->email)->send(new VpsExpiring($vps, 3));
                $count++;
            } catch (\Throwable $e) {
                Log::error('Failed to send expiration email', [
                    'vps_id' => $vps->id,
                    'msg' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Completed. Sent {$count} warning emails.");

        return 0;
    }
}
