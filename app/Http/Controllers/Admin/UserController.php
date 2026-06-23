<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TopupRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    // ── Danh sách user ──────────────────────────────────────────────
    public function index(Request $request)
    {
        $users = User::withCount(['vpsInstances', 'proxyInstances'])
            ->when($request->search, fn($q) => $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%'))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    // ── Chi tiết 1 user ─────────────────────────────────────────────
    public function show(User $user)
    {
        $user->loadCount(['vpsInstances', 'proxyInstances']);
        $proxies = $user->proxyInstances()->with('cloudSunnyAccount')->latest()->get();
        $topups = TopupRequest::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        return view('admin.users.show', compact('user', 'topups', 'proxies'));
    }

    // ── Điều chỉnh số dư ────────────────────────────────────────────
    public function adjustBalance(Request $request, User $user)
    {
        $request->validate([
            'amount' => 'required|integer|min:-100000000|max:100000000',
            'note'   => 'nullable|string|max:200',
        ]);

        $amount = (int) $request->amount;

        if ($amount < 0 && $user->balance + $amount < 0) {
            return back()->with('error', 'Số dư không đủ để trừ. Số dư hiện tại: ' . number_format($user->balance) . ' đ');
        }

        User::where('id', $user->id)->increment('balance', $amount);

        return back()->with('success',
            ($amount >= 0 ? 'Cộng ' : 'Trừ ') .
            number_format(abs($amount)) . ' đ vào tài khoản ' . $user->name . '.'
        );
    }

    // ── Toggle admin ────────────────────────────────────────────────
    public function toggleAdmin(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Không thể tự bỏ quyền admin của mình.');
        }

        $user->is_admin = !$user->is_admin;
        $user->save();

        return back()->with('success',
            $user->is_admin
                ? $user->name . ' đã được cấp quyền Admin.'
                : $user->name . ' đã bị thu hồi quyền Admin.'
        );
    }

    // ── Danh sách topup requests ─────────────────────────────────────
    public function topups(Request $request)
    {
        $topups = TopupRequest::with('user')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.topups', compact('topups'));
    }

    // ── Duyệt topup ─────────────────────────────────────────────────
    public function approveTopup(Request $request, TopupRequest $topup)
    {
        if (!$topup->isPending()) {
            return back()->with('error', 'Yêu cầu này đã được xử lý rồi.');
        }

        $request->validate([
            'approved_amount' => 'required|integer|min:1000',
            'admin_note'      => 'nullable|string|max:200',
        ]);

        DB::transaction(function () use ($request, $topup) {
            $amount = (int) $request->approved_amount;

            $topup->update([
                'status'          => 'approved',
                'approved_amount' => $amount,
                'admin_note'      => $request->admin_note,
                'approved_by'     => Auth::id(),
                'processed_at'    => now(),
            ]);

            User::where('id', $topup->user_id)->increment('balance', $amount);
        });

        return back()->with('success',
            'Đã duyệt nạp ' . number_format($request->approved_amount) . ' đ cho ' . $topup->user->name . '.'
        );
    }

    // ── Từ chối topup ────────────────────────────────────────────────
    public function rejectTopup(Request $request, TopupRequest $topup)
    {
        if (!$topup->isPending()) {
            return back()->with('error', 'Yêu cầu này đã được xử lý rồi.');
        }

        $topup->update([
            'status'       => 'rejected',
            'admin_note'   => $request->admin_note ?? 'Không đủ điều kiện.',
            'approved_by'  => Auth::id(),
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Đã từ chối yêu cầu nạp tiền.');
    }

    // ── Xóa User ─────────────────────────────────────────────────────
    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Không thể tự xóa tài khoản của chính mình.');
        }

        if ($user->vpsInstances()->exists() || $user->proxyInstances()->exists()) {
            return back()->with('error', 'Không thể xóa người dùng đang có VPS hoặc Proxy. Vui lòng xóa hết dịch vụ trước.');
        }

        $user->delete();

        return back()->with('success', 'Đã xóa người dùng thành công.');
    }
}
