<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VpsInstance;
use Illuminate\Http\Request;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        $query = VpsInstance::with('user', 'cloudSunnyAccount')
            ->orderByDesc('created_at');

        // Total metrics
        $totalRevenue = VpsInstance::sum('paid_amount');
        $totalCost = VpsInstance::sum('provider_cost');
        $totalProfit = $totalRevenue - $totalCost;

        // Current month metrics
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        
        // This is a simplified metric, tracking only current active plans or newly created ones. 
        // For accurate monthly billing we'd need a transaction table, but this is a good estimate based on active VPS.
        $monthRevenue = VpsInstance::where('created_at', '>=', $startOfMonth)
                                   ->where('created_at', '<=', $endOfMonth)
                                   ->sum('paid_amount');
        $monthCost = VpsInstance::where('created_at', '>=', $startOfMonth)
                                ->where('created_at', '<=', $endOfMonth)
                                ->sum('provider_cost');
        $monthProfit = $monthRevenue - $monthCost;

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('label', 'like', "%{$search}%")
                    ->orWhere('public_ip', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('email', 'like', "%{$search}%");
                    });
            });
        }

        $instances = $query->paginate(20)->appends($request->all());

        return view('admin.revenue.index', compact(
            'instances', 
            'totalRevenue', 
            'totalCost', 
            'totalProfit',
            'monthRevenue',
            'monthCost',
            'monthProfit'
        ));
    }
}
