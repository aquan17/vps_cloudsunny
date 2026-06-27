<?php

namespace App\Services;

use App\Models\User;
use App\Models\AffiliateLog;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class AffiliateService
{
    const COMMISSION_RATE = 0.05; // 5%

    /**
     * Process commission for a transaction
     * 
     * @param User $buyer The user who made the purchase
     * @param float $amount The total amount of the transaction
     * @param string $type The transaction type (e.g., 'buy', 'renew')
     * @return void
     */
    public function processCommission(User $buyer, $amount, $type)
    {
        if (!$buyer->referred_by || $amount <= 0) {
            return;
        }

        $referrer = User::find($buyer->referred_by);
        if (!$referrer) {
            return;
        }

        $commission = $amount * self::COMMISSION_RATE;
        if ($commission <= 0) {
            return;
        }

        DB::transaction(function () use ($referrer, $buyer, $amount, $commission, $type) {
            // Re-fetch referrer with lock for update
            $lockedReferrer = User::where('id', $referrer->id)->lockForUpdate()->first();
            
            if ($lockedReferrer) {
                $lockedReferrer->increment('balance', $commission);

                AffiliateLog::create([
                    'user_id' => $lockedReferrer->id,
                    'buyer_id' => $buyer->id,
                    'transaction_type' => $type,
                    'amount' => $amount,
                    'commission' => $commission,
                ]);

                Transaction::create([
                    'user_id' => $lockedReferrer->id,
                    'type' => 'affiliate_bonus',
                    'amount' => $commission,
                    'description' => "Hoa hồng giới thiệu từ tài khoản {$buyer->email}",
                ]);
            }
        });
    }
}
