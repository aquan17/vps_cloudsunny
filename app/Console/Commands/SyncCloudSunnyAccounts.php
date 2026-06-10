<?php

namespace App\Console\Commands;

use App\Models\CloudSunnyAccount;
use App\Services\CloudSunnyAccountSyncService;
use App\Services\CloudSunnyApiService;
use Illuminate\Console\Command;

class SyncCloudSunnyAccounts extends Command
{
    protected $signature = 'cloudsunny:sync-accounts';
    protected $description = 'Dong bo so du CloudSunny agency accounts';

    public function handle(CloudSunnyApiService $api, CloudSunnyAccountSyncService $sync): int
    {
        foreach (CloudSunnyAccount::where('is_active', true)->get() as $account) {
            try {
                $sync->sync($account, $api);
                $this->info("OK: {$account->label}");
            } catch (\Throwable $e) {
                $account->sync_error = $e->getMessage();
                $account->save();
                $this->error("FAIL: {$account->label} - {$e->getMessage()}");
            }
        }

        return 0;
    }
}
