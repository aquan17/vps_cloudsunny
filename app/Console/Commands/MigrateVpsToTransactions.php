<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Models\VpsInstance;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateVpsToTransactions extends Command
{
    protected $signature = 'vps:migrate-transactions';
    protected $description = 'Chuyển đổi dữ liệu paid_amount của VPS thành transactions để không bị mất doanh thu khi xóa VPS';

    public function handle()
    {
        $this->info('Starting migration...');

        $vpsInstances = VpsInstance::all();
        $count = 0;

        DB::transaction(function () use ($vpsInstances, &$count) {
            foreach ($vpsInstances as $vps) {
                // Check if already migrated to avoid duplicates if run multiple times
                $exists = Transaction::where('vps_instance_id', $vps->id)->where('type', 'buy')->exists();
                if (!$exists && $vps->paid_amount > 0) {
                    Transaction::create([
                        'user_id' => $vps->user_id,
                        'vps_instance_id' => $vps->id,
                        'type' => 'buy',
                        'amount' => $vps->paid_amount,
                        'provider_cost' => $vps->provider_cost,
                        'description' => "Đồng bộ VPS cũ: {$vps->label} ({$vps->billing_cycle})",
                        'created_at' => $vps->created_at,
                        'updated_at' => $vps->updated_at,
                    ]);
                    $count++;
                }
            }
        });

        $this->info("Successfully migrated {$count} VPS instances to transactions.");
        return 0;
    }
}
