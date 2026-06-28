@extends('layouts.app')
@section('title', 'Quản lý Affiliate (F1) — SeaServer Admin')

@section('breadcrumbs')
    <span>Khách hàng & Affiliate</span>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-900">Affiliate (F1)</span>
@endsection

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Quản lý Affiliate (F1)</h1>
    <p class="text-sm text-gray-500 mt-1">Danh sách người dùng đã được giới thiệu (F1).</p>
</div>

<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4">Ngày đăng ký</th>
                    <th class="px-6 py-4">Khách hàng (F1)</th>
                    <th class="px-6 py-4">Người giới thiệu</th>
                    <th class="px-6 py-4 text-right">Hoa hồng tạo ra</th>
                    <th class="px-6 py-4">Trạng thái</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($referrals as $ref)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-gray-500">
                        {{ $ref->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">{{ $ref->name }}</div>
                        <div class="text-xs text-gray-500">{{ $ref->email }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @if($ref->referrer)
                            <div class="font-medium text-cloud-600">{{ $ref->referrer->name }}</div>
                            <div class="text-xs text-gray-500">{{ $ref->referrer->email }}</div>
                        @else
                            <span class="text-gray-400 italic">Không có</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right font-bold text-green-600">
                        {{ number_format($ref->total_commission ?? 0) }} đ
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Hoạt động</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        Chưa có dữ liệu giới thiệu nào.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($referrals->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
            {{ $referrals->links() }}
        </div>
    @endif
</div>
@endsection
