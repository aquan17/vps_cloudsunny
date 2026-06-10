<?php

namespace App\Services;

use App\Models\CloudSunnyAccount;

class CloudSunnyAccountRouter
{
    public function firstActive(): ?CloudSunnyAccount
    {
        $account = CloudSunnyAccount::query()
            ->where('is_active', true)
            ->where('is_full', false)
            ->orderBy('priority')
            ->orderBy('id')
            ->first();

        return $account ?: $this->envAccount();
    }

    public function pickForOrder(int $amountVnd): ?CloudSunnyAccount
    {
        $account = CloudSunnyAccount::query()
            ->where('is_active', true)
            ->where('is_full', false)
            ->where('credit_vnd', '>=', $amountVnd)
            ->orderBy('priority')
            ->orderBy('id')
            ->first()
            ?? $this->firstActive();

        return $account;
    }

    private function envAccount(): ?CloudSunnyAccount
    {
        $username = config('cloudsunny.api_username');
        $app = config('cloudsunny.api_app');
        $secret = config('cloudsunny.api_secret');

        if (!$username || !$app || !$secret) {
            return null;
        }

        return CloudSunnyAccount::firstOrCreate(
            ['api_username' => $username, 'api_app' => $app],
            [
                'label' => 'CloudSunny ENV',
                'api_secret' => $secret,
                'is_active' => true,
                'priority' => 0,
            ]
        );
    }
}
