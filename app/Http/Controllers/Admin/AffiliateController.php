<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AffiliateController extends Controller
{
    public function index()
    {
        $referrals = User::whereNotNull('referred_by')
            ->with('referrer')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Calculate total commission for each referral
        foreach ($referrals as $ref) {
            $ref->total_commission = \App\Models\AffiliateLog::where('buyer_id', $ref->id)
                                        ->where('user_id', $ref->referred_by)
                                        ->sum('commission');
        }

        return view('admin.affiliates.index', compact('referrals'));
    }
}
