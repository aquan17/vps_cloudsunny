<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with('user', 'vps')
            ->orderByDesc('created_at');

        // Total metrics
        $totalRevenue = Transaction::whereIn('type', ['buy', 'renew', 'upgrade'])->sum('amount');
        $totalCost = Transaction::whereIn('type', ['buy', 'renew', 'upgrade'])->sum('provider_cost');
        $totalProfit = $totalRevenue - $totalCost;

        // Current month metrics
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        
        $monthRevenue = Transaction::whereIn('type', ['buy', 'renew', 'upgrade'])
                                   ->where('created_at', '>=', $startOfMonth)
                                   ->where('created_at', '<=', $endOfMonth)
                                   ->sum('amount');
        $monthCost = Transaction::whereIn('type', ['buy', 'renew', 'upgrade'])
                                ->where('created_at', '>=', $startOfMonth)
                                ->where('created_at', '<=', $endOfMonth)
                                ->sum('provider_cost');
        $monthProfit = $monthRevenue - $monthCost;

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('vps', function ($vq) use ($search) {
                        $vq->where('label', 'like', "%{$search}%")
                           ->orWhere('public_ip', 'like', "%{$search}%");
                    });
            });
        }

        $transactions = $query->paginate(20)->appends($request->all());

        return view('admin.revenue.index', compact(
            'transactions', 
            'totalRevenue', 
            'totalCost', 
            'totalProfit',
            'monthRevenue',
            'monthCost',
            'monthProfit'
        ));
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return back()->with('success', 'Đã xóa giao dịch thành công. Doanh thu đã được cập nhật.');
    }
}
