<?php

namespace App\Services;

use App\Models\CloudSunnyAccount;

class CloudSunnyAccountSyncService
{
    public function sync(CloudSunnyAccount $account, CloudSunnyApiService $api): void
    {
        $api->forAccount($account)->authenticate($account);
        $data = $api->forAccount($account)->getAgencyInfo();

        $user = $data['user'] ?? $data;
        $credit = $user['credit'] ?? [];

        $account->credit_vnd = (int) ($credit['credit'] ?? $account->credit_vnd);
        $account->total_vnd = (int) ($credit['total'] ?? $account->total_vnd);
        $account->last_synced_at = now();
        $account->sync_error = null;
        $account->save();
    }
}
