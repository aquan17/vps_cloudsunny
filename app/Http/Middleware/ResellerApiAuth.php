<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ResellerApiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized: Missing API Key'
            ], 401);
        }

        $user = User::where('api_key', $token)->first();

        if (!$user) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized: Invalid API Key'
            ], 401);
        }

        // Đảm bảo user này có quyền Reseller hoặc Admin
        if (!$user->isReseller() && !$user->isAdmin()) {
            return response()->json([
                'status' => 403,
                'message' => 'Forbidden: Account does not have API permission'
            ], 403);
        }

        // Đăng nhập tạm thời User vào Request hiện tại
        Auth::login($user);

        return $next($request);
    }
}
