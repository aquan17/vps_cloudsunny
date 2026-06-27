<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AffiliateLog;

class AffiliateController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Tự động tạo mã giới thiệu cho user cũ nếu chưa có
        if (empty($user->ref_code)) {
            $user->ref_code = strtoupper(\Illuminate\Support\Str::random(8));
            $user->save();
        }
        $totalCommission = AffiliateLog::where('user_id', $user->id)->sum('commission');
        $referralCount = $user->referrals()->count();
        
        $logs = AffiliateLog::where('user_id', $user->id)
            ->with('buyer')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('affiliate.index', compact('user', 'totalCommission', 'referralCount', 'logs'));
    }
}
