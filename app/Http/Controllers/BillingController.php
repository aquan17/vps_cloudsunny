<?php

namespace App\Http\Controllers;

use App\Models\TopupRequest;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class BillingController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Lấy các lần nạp tiền thành công (đã paid)
        $topups = TopupRequest::where('user_id', $user->id)
            ->whereIn('status', ['paid', 'approved'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($r) {
                return [
                    'category'    => 'topup',
                    'icon'        => 'plus',
                    'amount'      => (int) ($r->approved_amount ?? $r->amount),
                    'description' => 'Nạp tiền qua ' . $this->providerLabel($r->provider),
                    'at'          => $r->paid_at ?? $r->created_at,
                    'status'      => 'success',
                    'ref'         => $r->code,
                    'meta'        => $r,
                ];
            })
            ->toBase();

        // Lấy tất cả giao dịch chi tiêu
        $transactions = Transaction::where('user_id', $user->id)
            ->with(['vps', 'proxy'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($t) {
                return [
                    'category'    => $this->transactionCategory($t->type),
                    'icon'        => 'minus',
                    'amount'      => -(int) $t->amount,
                    'description' => $t->description,
                    'at'          => $t->created_at,
                    'status'      => 'spend',
                    'ref'         => '#' . $t->id,
                    'meta'        => $t,
                ];
            })
            ->toBase();

        // Gộp + sắp xếp mới nhất trước
        $timeline = $topups->merge($transactions)
            ->sortByDesc(function ($item) {
                $at = $item['at'];
                return $at ? $at->timestamp : 0;
            })
            ->values();

        // Tính tổng thống kê
        $totalTopup   = $topups->sum(function ($t) { return $t['amount']; });
        $totalSpend   = $transactions->sum(function ($t) { return abs($t['amount']); });
        $currentBalance = (int) $user->balance;

        return view('billing.index', compact(
            'timeline',
            'totalTopup',
            'totalSpend',
            'currentBalance'
        ));
    }

    private function providerLabel(?string $provider): string
    {
        $labels = [
            'payos'  => 'PayOS',
            'manual' => 'Admin',
        ];
        $key = strtolower((string) $provider);
        return $labels[$key] ?? ucfirst((string) $provider ?: 'ngân hàng');
    }

    private function transactionCategory(string $type): string
    {
        $map = [
            'buy'     => 'buy',
            'renew'   => 'renew',
            'upgrade' => 'upgrade',
            'refund'  => 'refund',
        ];
        return $map[$type] ?? 'spend';
    }
}
