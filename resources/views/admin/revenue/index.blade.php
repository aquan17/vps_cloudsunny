@extends('layouts.app')
@section('title', 'Doanh thu & Lợi nhuận - Admin')

@section('breadcrumbs')
    <span>Quản trị</span>
    <span class="mx-2 text-gray-300">/</span>
    <span class="text-gray-900">Doanh thu</span>
@endsection

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Thống kê Doanh thu</h1>
        <p class="text-sm text-gray-500 mt-1">Báo cáo tổng quan về doanh thu và lợi nhuận trên toàn hệ thống.</p>
    </div>
</div>

<h2 class="text-lg font-bold text-gray-900 mb-4">Tổng quan Toàn thời gian</h2>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Tổng doanh thu</p>
        <p class="text-2xl font-extrabold text-blue-600">{{ number_format($totalRevenue) }} đ</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Tổng chi phí (gốc)</p>
        <p class="text-2xl font-extrabold text-red-600">{{ number_format($totalCost) }} đ</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Tổng lợi nhuận</p>
        <p class="text-2xl font-extrabold text-green-600">{{ number_format($totalProfit) }} đ</p>
    </div>
</div>

<h2 class="text-lg font-bold text-gray-900 mb-4">Tháng này (Kể từ {{ now()->startOfMonth()->format('d/m/Y') }})</h2>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Doanh thu tháng</p>
        <p class="text-xl font-extrabold text-gray-900">{{ number_format($monthRevenue) }} đ</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Chi phí tháng</p>
        <p class="text-xl font-extrabold text-gray-900">{{ number_format($monthCost) }} đ</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Lợi nhuận tháng</p>
        <p class="text-xl font-extrabold text-green-600">{{ number_format($monthProfit) }} đ</p>
    </div>
</div>

<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mb-8">
    <div class="px-6 py-5 border-b border-gray-200 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gray-50/50">
        <h2 class="text-base font-bold text-gray-900">Chi tiết từng giao dịch VPS</h2>
        <form method="GET" action="{{ route('admin.revenue.index') }}" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên, IP, Email..." class="px-3 py-1.5 border border-gray-300 rounded-md text-sm">
            <button type="submit" class="px-3 py-1.5 bg-gray-900 text-white rounded-md text-sm font-semibold">Tìm</button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left min-w-[1000px]">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">VPS</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Khách hàng</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Account cấp</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase text-right">Doanh thu (Bán)</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase text-right">Chi phí (Gốc)</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase text-right">Lợi nhuận</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase text-right">Biên lãi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($instances as $vps)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900 text-sm">{{ $vps->label }}</div>
                            <div class="text-xs text-gray-500">{{ $vps->public_ip ?: 'Chưa có IP' }}</div>
                            <div class="text-[11px] text-gray-400 mt-0.5">{{ $vps->status }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $vps->user->email ?? 'N/A' }}</div>
                            <div class="text-[11px] text-gray-500">{{ $vps->created_at->format('d/m/Y') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs font-semibold text-gray-700">{{ $vps->cloudSunnyAccount->label ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="font-mono font-bold text-blue-600 text-sm">{{ number_format($vps->paid_amount) }}</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="font-mono font-bold text-red-600 text-sm">{{ number_format($vps->provider_cost) }}</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @php $profit = $vps->paid_amount - $vps->provider_cost; @endphp
                            <div class="font-mono font-bold text-sm {{ $profit > 0 ? 'text-green-600' : ($profit < 0 ? 'text-red-600' : 'text-gray-500') }}">
                                {{ number_format($profit) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @php 
                                $margin = $vps->paid_amount > 0 ? round(($profit / $vps->paid_amount) * 100, 1) : 0;
                            @endphp
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $margin > 0 ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $margin }}%
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">Chưa có giao dịch nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($instances->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $instances->links() }}
        </div>
    @endif
</div>
@endsection
