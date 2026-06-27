<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class CheckAffiliate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->has('ref')) {
            // Save the referral code in a cookie for 30 days (43200 minutes)
            Cookie::queue('referral_code', $request->query('ref'), 43200);
        }

        return $next($request);
    }
}
