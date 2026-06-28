<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResellerApiController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->check() && (auth()->user()->isReseller() || auth()->user()->isAdmin())) {
                return $next($request);
            }
            abort(403, 'Bạn không có quyền truy cập khu vực này.');
        });
    }

    public function index()
    {
        $user = Auth::user();
        
        // Sinh API Key tự động nếu chưa có
        if (empty($user->api_key)) {
            $key = 'sk_' . \Illuminate\Support\Str::random(40);
            $user->api_key = $key;
            $user->save();
        }
        
        $apiKey = $user->api_key;
        $webhookUrl = $user->webhook_url;
        
        return view('reseller-api.index', compact('user', 'apiKey', 'webhookUrl'));
    }

    public function updateWebhook(Request $request)
    {
        $request->validate([
            'webhook_url' => 'nullable|url'
        ]);

        $user = Auth::user();
        $user->webhook_url = $request->webhook_url;
        $user->save();

        return redirect()->back()->with('success', 'Cập nhật Webhook URL thành công!');
    }
}
