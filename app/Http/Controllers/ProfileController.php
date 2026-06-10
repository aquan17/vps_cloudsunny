<?php

namespace App\Http\Controllers;

use App\Models\TopupRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $activeVpsCount = $user->vpsInstances()->whereIn('status', ['running', 'provisioning'])->count();
        $totalDeposited = TopupRequest::where('user_id', $user->id)->where('status', 'approved')->sum('amount');
        
        return view('profile.index', compact('user', 'activeVpsCount', 'totalDeposited'));
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'current_password.current_password' => 'Mật khẩu hiện tại không chính xác.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Đổi mật khẩu thành công.');
    }
}
