@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-8">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Quản lý Proxy</h1>
            <p class="text-sm text-gray-500 mt-1">Danh sách Proxy bạn đang sử dụng</p>
        </div>
        <div>
            <a href="{{ route('proxy.store.index') }}" class="inline-flex items-center gap-2 bg-cloud-600 hover:bg-cloud-700 text-white font-medium py-2 px-4 rounded-md transition-colors text-sm shadow-sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Mua Proxy Mới
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-sm">
                        <th class="p-4 font-semibold text-gray-600">ID</th>
                        <th class="p-4 font-semibold text-gray-600">IP / Port</th>
                        <th class="p-4 font-semibold text-gray-600">Gói / Chu kỳ</th>
                        <th class="p-4 font-semibold text-gray-600">Ngày hết hạn</th>
                        <th class="p-4 font-semibold text-gray-600">Trạng thái</th>
                        <th class="p-4 font-semibold text-gray-600">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($instances as $instance)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-4 font-medium text-gray-900">#{{ $instance->id }}</td>
                        <td class="p-4">
                            @if($instance->ip)
                                <div class="font-medium text-gray-900">{{ $instance->ip }}</div>
                                <div class="text-xs text-gray-500 mt-1">HTTP: {{ $instance->port ?? '-' }}</div>
                            @else
                                <span class="text-gray-400 italic">Đang cấp phát...</span>
                            @endif
                        </td>
                        <td class="p-4">
                            <div class="font-medium text-gray-900">PID: {{ $instance->product_id }}</div>
                            <div class="text-xs text-gray-500 mt-1 uppercase">{{ $instance->billing_cycle }}</div>
                        </td>
                        <td class="p-4">
                            @if($instance->expires_at)
                                <div class="font-medium {{ \Carbon\Carbon::parse($instance->expires_at)->isPast() ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ \Carbon\Carbon::parse($instance->expires_at)->format('d/m/Y') }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ \Carbon\Carbon::parse($instance->expires_at)->diffForHumans() }}
                                </div>
                            @else
                                -
                            @endif
                        </td>
                        <td class="p-4">
                            @if(strtolower($instance->status) == 'hoạt động')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    Hoạt động
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 animate-pulse"></span>
                                    {{ $instance->status }}
                                </span>
                            @endif
                        </td>
                        <td class="p-4">
                            <a href="{{ route('proxy.show', $instance->id) }}" class="inline-flex items-center justify-center bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-3 py-1.5 rounded text-sm font-medium transition-colors shadow-sm">
                                Chi tiết
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.974 0-5.699-.588-8.083-1.582"></path></svg>
                                <p class="text-base font-medium text-gray-900">Bạn chưa có Proxy nào</p>
                                <p class="text-sm mt-1">Hãy bắt đầu bằng cách thuê một Proxy mới.</p>
                                <a href="{{ route('proxy.store.index') }}" class="mt-4 text-cloud-600 hover:text-cloud-700 font-medium text-sm">Đi đến cửa hàng &rarr;</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
