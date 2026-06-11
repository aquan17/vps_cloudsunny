@extends('layouts.app')
@section('title', 'Máy chủ VPS — SeaServer')

@section('breadcrumbs')
    <span>Máy chủ VPS</span>
@endsection

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Máy chủ VPS</h1>
        <p class="text-sm text-gray-500 mt-1">Quản lý và giám sát các máy chủ ảo của bạn.</p>
    </div>
    
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
        <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-64">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm VPS..." class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:border-cloud-600 focus:ring-1 focus:ring-cloud-600 bg-white shadow-sm">
            </div>
            <button type="submit" class="px-4 py-2 border border-gray-300 rounded-md bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm transition-colors flex-shrink-0">
                Tìm kiếm
            </button>
        </form>
        <a href="{{ route('pricing') }}" class="flex items-center justify-center gap-2 bg-cloud-600 hover:bg-cloud-700 text-white font-medium py-2 px-4 rounded-md transition-colors text-sm shadow-sm w-full sm:w-auto">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Khởi tạo
        </a>
    </div>
</div>

@if($instances->isEmpty())
    <div class="bg-white border border-gray-200 rounded-xl p-12 text-center shadow-sm">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-16.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a4.5 4.5 0 01.9-2.7L5.737 5.1a3.375 3.375 0 012.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 01.9 2.7m0 0a3 3 0 01-3 3m0 3h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008zm-3 6h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008z" /></svg>
        </div>
        @if(request('search'))
            <h3 class="text-lg font-semibold text-gray-900 mb-1">Không tìm thấy kết quả</h3>
            <p class="text-gray-500 mb-6 text-sm">Không có máy chủ VPS nào khớp với từ khóa "{{ request('search') }}".</p>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-2 px-4 rounded-md transition-colors text-sm shadow-sm">
                Xóa tìm kiếm
            </a>
        @else
            <h3 class="text-lg font-semibold text-gray-900 mb-1">Chưa có VPS nào</h3>
            <p class="text-gray-500 mb-6 text-sm">Bạn chưa khởi tạo máy chủ ảo nào.</p>
            <a href="{{ route('pricing') }}" class="inline-flex items-center gap-2 bg-cloud-600 hover:bg-cloud-700 text-white font-medium py-2 px-4 rounded-md transition-colors text-sm shadow-sm">
                Khởi tạo VPS đầu tiên
            </a>
        @endif
    </div>
@else
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        
        {{-- DESKTOP TABLE VIEW --}}
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50/50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Tên & Khu vực</th>
                        <th class="px-6 py-4">Địa chỉ IP</th>
                        <th class="px-6 py-4">Cấu hình</th>
                        <th class="px-6 py-4">Thời gian</th>
                        <th class="px-6 py-4">Trạng thái</th>
                        <th class="px-6 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($instances as $vps)
                        @php
                            $statusLower = mb_strtolower($vps->status, 'UTF-8');
                            $isOk = strpos($statusLower, 'running') !== false || strpos($statusLower, 'hoạt động') !== false || strpos($statusLower, 'sẵn sàng') !== false || strpos($statusLower, 'sẵn sàng') !== false;
                            $isErr = strpos($statusLower, 'lỗi') !== false || strpos($statusLower, 'offline') !== false || strpos($statusLower, 'đã tắt') !== false || strpos($statusLower, 'đã tắt') !== false;
                            $statusBg = $isOk ? 'bg-green-50 text-green-700' : ($isErr ? 'bg-red-50 text-red-700' : 'bg-yellow-50 text-yellow-700');
                            $statusDot = $isOk ? 'bg-green-500' : ($isErr ? 'bg-red-500' : 'bg-yellow-500');
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors group cursor-pointer" onclick="window.location='{{ route('dashboard.show', $vps) }}'">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-md bg-cloud-50 flex items-center justify-center text-cloud-600 flex-shrink-0">
                                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 17.25v-.228a4.5 4.5 0 00-.12-1.03l-2.268-9.64a3.375 3.375 0 00-3.285-2.602H7.923a3.375 3.375 0 00-3.285 2.602l-2.268 9.64a4.5 4.5 0 00-.12 1.03v.228m19.5 0a3 3 0 01-3 3H5.25a3 3 0 01-3-3m19.5 0a3 3 0 00-3-3H5.25a3 3 0 00-3 3m16.5 0h.008v.008h-.008v-.008zm-3 0h.008v.008h-.008v-.008z" /></svg>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900">{{ $vps->label }}</div>
                                        <div class="flex items-center gap-1 text-xs text-gray-500 mt-0.5">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                                            {{ $vps->region ?? 'Không rõ' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($vps->public_ip)
                                    <span class="inline-flex bg-gray-100 text-gray-600 font-mono text-xs px-2.5 py-1 rounded-md border border-gray-200">
                                        {{ $vps->public_ip }}
                                    </span>
                                @else
                                    <span class="text-gray-400 font-mono text-xs">Đang chờ...</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3 text-gray-600">
                                    <div class="flex flex-col items-center">
                                        <span class="font-bold text-gray-900">{{ $vps->ram ?? 1 }} GB</span>
                                        <span class="text-[10px] text-gray-400 uppercase">RAM</span>
                                    </div>
                                    <span class="text-gray-300">•</span>
                                    <div class="flex flex-col items-center">
                                        <span class="font-bold text-gray-900">{{ $vps->cpu ?? 1 }}</span>
                                        <span class="text-[10px] text-gray-400 uppercase">vCPU</span>
                                    </div>
                                    <span class="text-gray-300">•</span>
                                    <div class="flex flex-col items-center">
                                        <span class="font-bold text-gray-900">{{ $vps->disk ?? 25 }}GB</span>
                                        <span class="text-[10px] text-gray-400 uppercase">SSD</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs text-gray-500 mb-1">Tạo: <span class="font-medium text-gray-700">{{ $vps->created_at->format('d/m/Y') }}</span></div>
                                <div class="text-xs text-gray-500">Hết hạn: <span class="font-medium {{ $vps->expires_at && $vps->expires_at->isPast() ? 'text-red-600' : 'text-gray-700' }}">{{ $vps->expires_at ? $vps->expires_at->format('d/m/Y') : 'Vĩnh viễn' }}</span></div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusBg }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $statusDot }}"></span>
                                    {{ $vps->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('dashboard.show', $vps) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-gray-200 rounded-md text-xs font-semibold text-gray-700 hover:bg-gray-50 hover:text-cloud-600 transition-colors shadow-sm" onclick="event.stopPropagation()">
                                    Xem chi tiết
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- MOBILE BOX VIEW --}}
        <div class="block lg:hidden divide-y divide-gray-100">
            @foreach($instances as $vps)
                @php
                    $statusLower = mb_strtolower($vps->status, 'UTF-8');
                    $isOk = strpos($statusLower, 'running') !== false || strpos($statusLower, 'hoạt động') !== false || strpos($statusLower, 'sẵn sàng') !== false || strpos($statusLower, 'sẵn sàng') !== false;
                    $isErr = strpos($statusLower, 'lỗi') !== false || strpos($statusLower, 'offline') !== false || strpos($statusLower, 'đã tắt') !== false || strpos($statusLower, 'đã tắt') !== false;
                    $statusBg = $isOk ? 'bg-green-50 text-green-700' : ($isErr ? 'bg-red-50 text-red-700' : 'bg-yellow-50 text-yellow-700');
                    $statusDot = $isOk ? 'bg-green-500' : ($isErr ? 'bg-red-500' : 'bg-yellow-500');
                @endphp
                <div class="p-5 hover:bg-gray-50/50 transition-colors cursor-pointer" onclick="window.location='{{ route('dashboard.show', $vps) }}'">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-md bg-cloud-50 flex items-center justify-center text-cloud-600 flex-shrink-0">
                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 17.25v-.228a4.5 4.5 0 00-.12-1.03l-2.268-9.64a3.375 3.375 0 00-3.285-2.602H7.923a3.375 3.375 0 00-3.285 2.602l-2.268 9.64a4.5 4.5 0 00-.12 1.03v.228m19.5 0a3 3 0 01-3 3H5.25a3 3 0 01-3-3m19.5 0a3 3 0 00-3-3H5.25a3 3 0 00-3 3m16.5 0h.008v.008h-.008v-.008zm-3 0h.008v.008h-.008v-.008z" /></svg>
                            </div>
                            <div>
                                <div class="font-bold text-gray-900">{{ $vps->label }}</div>
                                <div class="flex items-center gap-1 text-xs text-gray-500 mt-0.5">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                                    {{ $vps->region ?? 'Không rõ' }}
                                </div>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusBg }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $statusDot }}"></span>
                            {{ $vps->status }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <span class="block text-xs font-medium text-gray-500 mb-1">Địa chỉ IP</span>
                            @if($vps->public_ip)
                                <span class="inline-flex bg-gray-100 text-gray-600 font-mono text-xs px-2.5 py-1 rounded-md border border-gray-200">
                                    {{ $vps->public_ip }}
                                </span>
                            @else
                                <span class="text-gray-400 font-mono text-xs">Đang chờ...</span>
                            @endif
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-500 mb-1">Cấu hình</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $vps->ram ?? 1 }}GB RAM · {{ $vps->cpu ?? 1 }}vCPU</span>
                        </div>
                        <div class="col-span-2 border-t border-gray-100 pt-3 mt-1">
                            <span class="block text-xs font-medium text-gray-500 mb-1">Thời gian</span>
                            <div class="flex justify-between text-xs">
                                <div><span class="text-gray-500">Tạo:</span> <span class="font-medium text-gray-900">{{ $vps->created_at->format('d/m/Y') }}</span></div>
                                <div><span class="text-gray-500">Hết hạn:</span> <span class="font-medium {{ $vps->expires_at && $vps->expires_at->isPast() ? 'text-red-600' : 'text-gray-900' }}">{{ $vps->expires_at ? $vps->expires_at->format('d/m/Y') : 'Vĩnh viễn' }}</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-50 text-center">
                        <a href="{{ route('dashboard.show', $vps) }}" class="inline-flex justify-center items-center gap-1.5 w-full py-2 bg-cloud-50 text-cloud-700 rounded-md text-sm font-semibold hover:bg-cloud-100 transition-colors" onclick="event.stopPropagation()">
                            Xem chi tiết
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/30 flex items-center justify-between">
            <span class="text-xs text-gray-500 hidden sm:inline">
                Đang hiển thị từ {{ $instances->firstItem() ?? 0 }} đến {{ $instances->lastItem() ?? 0 }} trong tổng số {{ $instances->total() }} VPS
            </span>
            <div class="flex gap-2 w-full sm:w-auto overflow-x-auto justify-center">
                {{ $instances->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
@endif
@endsection
