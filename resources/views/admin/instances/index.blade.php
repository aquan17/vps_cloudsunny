@extends('layouts.app')
@section('title', 'Quản lý Máy chủ VPS — Admin')

@section('breadcrumbs')
    <span>Quản trị</span>
    <span class="mx-2 text-gray-300">/</span>
    <span class="text-gray-900">Máy chủ VPS</span>
@endsection

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Máy chủ VPS Toàn hệ thống</h1>
        <p class="text-sm text-gray-500 mt-1">Tổng cộng: {{ $instances->total() }} máy chủ đang được quản lý.</p>
    </div>
</div>

<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden flex flex-col mb-6">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.instances.index') }}" class="flex items-center gap-2 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-80">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên VPS, IP, người dùng..." class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:border-cloud-600 focus:ring-1 focus:ring-cloud-600 bg-white shadow-sm">
            </div>
            <button type="submit" class="px-4 py-2 border border-gray-300 rounded-md bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm transition-colors flex-shrink-0">
                Tìm kiếm
            </button>
        </form>
    </div>

    {{-- DESKTOP TABLE VIEW --}}
    <div class="hidden lg:block overflow-x-auto flex-1">
        <table class="w-full text-left border-collapse min-w-[900px]">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">VPS / Vị trí</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Chủ sở hữu</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Cấu hình</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Tài khoản Node</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Thời gian</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($instances as $vps)
                @php
                    $statusLower = mb_strtolower($vps->status, 'UTF-8');
                    $isOk = strpos($statusLower, 'running') !== false || strpos($statusLower, 'hoạt động') !== false || strpos($statusLower, 'sẵn sàng') !== false || strpos($statusLower, 'sẵn sàng') !== false;
                    $isErr = strpos($statusLower, 'lỗi') !== false || strpos($statusLower, 'offline') !== false || strpos($statusLower, 'đã tắt') !== false || strpos($statusLower, 'đã tắt') !== false;
                    $statusBg = $isOk ? 'bg-green-50 text-green-700 border-green-200' : ($isErr ? 'bg-red-50 text-red-700 border-red-200' : 'bg-yellow-50 text-yellow-700 border-yellow-200');
                @endphp
            <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-6 py-4">
                    <div class="font-bold text-gray-900 text-sm mb-0.5">{{ $vps->label }}</div>
                    <div class="text-xs text-gray-500">{{ $vps->public_ip ?? 'Đang chờ IP...' }}</div>
                    <div class="mt-1 text-[10px] text-gray-400 font-mono">{{ $vps->region ?? '—' }}</div>
                </td>
                <td class="px-6 py-4">
                    @if($vps->user)
                        <a href="{{ route('admin.users.show', $vps->user_id) }}" class="font-bold text-gray-900 text-sm hover:text-cloud-600 transition-colors block mb-0.5">{{ $vps->user->name }}</a>
                        <div class="text-xs text-gray-500">{{ $vps->user->email }}</div>
                    @else
                        <span class="text-gray-400 italic">Không có</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm font-semibold text-gray-900">{{ $vps->ram ?? 1 }}GB RAM · {{ $vps->cpu ?? 1 }} vCPU</div>
                    <div class="text-xs text-gray-500 mt-0.5">{{ $vps->disk ?? 25 }}GB SSD</div>
                </td>
                <td class="px-6 py-4">
                    @if($vps->cloudSunnyAccount)
                        <div class="text-sm font-semibold text-gray-700">{{ $vps->cloudSunnyAccount->label }}</div>
                        <div class="text-[10px] text-gray-400 font-mono mt-0.5">{{ $vps->cloudSunnyAccount->api_username ?? '' }}</div>
                    @else
                        <span class="text-gray-400 italic">Không rõ</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <div class="text-[11px] text-gray-500 mb-0.5">Tạo: <span class="font-semibold text-gray-700">{{ $vps->created_at->format('d/m/Y') }}</span></div>
                    <div class="text-[11px] text-gray-500">Hết: <span class="font-semibold {{ $vps->expires_at && $vps->expires_at->isPast() ? 'text-red-600' : 'text-gray-700' }}">{{ $vps->expires_at ? $vps->expires_at->format('d/m/Y') : 'Vĩnh viễn' }}</span></div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold border {{ $statusBg }}">
                        {{ $vps->status }}
                    </span>
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('dashboard.show', $vps) }}" class="inline-flex items-center justify-center px-3 py-1.5 border border-cloud-200 rounded text-xs font-semibold text-cloud-700 bg-cloud-50 hover:bg-cloud-100 transition-colors">
                            Xem
                        </a>
                        <form method="POST" action="{{ route('dashboard.destroy', $vps) }}" data-confirm="Xóa VPS {{ $vps->label }} của người dùng này? Thao tác này không thể hoàn tác!">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center justify-center px-3 py-1.5 border border-red-200 rounded text-xs font-semibold text-red-700 bg-red-50 hover:bg-red-100 transition-colors">
                                Xóa
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-16.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a4.5 4.5 0 01.9-2.7L5.737 5.1a3.375 3.375 0 012.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 01.9 2.7m0 0a3 3 0 01-3 3m0 3h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008zm-3 6h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008z" /></svg>
                    @if(request('search'))
                        <p class="text-gray-900 font-semibold mb-1">Không tìm thấy kết quả</p>
                        <p>Không có máy chủ VPS nào khớp với từ khóa "{{ request('search') }}".</p>
                        <a href="{{ route('admin.instances.index') }}" class="inline-flex items-center gap-2 mt-4 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-1.5 px-3 rounded-md transition-colors text-sm shadow-sm">
                            Xóa tìm kiếm
                        </a>
                    @else
                        <p>Chưa có máy chủ VPS nào.</p>
                    @endif
                </td>
            </tr>
            @endforelse
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
                $statusBg = $isOk ? 'bg-green-50 text-green-700 border-green-200' : ($isErr ? 'bg-red-50 text-red-700 border-red-200' : 'bg-yellow-50 text-yellow-700 border-yellow-200');
            @endphp
        <div class="p-4 hover:bg-gray-50/50 transition-colors">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <div class="font-bold text-gray-900 text-sm mb-0.5">{{ $vps->label }}</div>
                    <div class="text-xs text-gray-500 font-mono">{{ $vps->public_ip ?? 'Đang chờ IP...' }}</div>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold border {{ $statusBg }}">
                    {{ $vps->status }}
                </span>
            </div>

            <div class="grid grid-cols-2 gap-2 text-xs mb-3 border-t border-gray-100 pt-3">
                <div>
                    <span class="text-gray-500 block mb-0.5">Chủ sở hữu:</span>
                    @if($vps->user)
                        <a href="{{ route('admin.users.show', $vps->user_id) }}" class="font-semibold text-gray-900 hover:text-cloud-600 block truncate">{{ $vps->user->name }}</a>
                    @else
                        <span class="text-gray-400 italic">—</span>
                    @endif
                </div>
                <div>
                    <span class="text-gray-500 block mb-0.5">Thao tác:</span>
                    <div class="flex gap-2">
                        <a href="{{ route('dashboard.show', $vps) }}" class="inline-flex items-center justify-center px-3 py-1.5 border border-cloud-200 rounded text-[10px] font-semibold text-cloud-700 bg-cloud-50 hover:bg-cloud-100 transition-colors">
                            Xem
                        </a>
                        <form method="POST" action="{{ route('dashboard.destroy', $vps) }}" data-confirm="Xóa VPS {{ $vps->label }} của người dùng này?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center justify-center px-3 py-1.5 border border-red-200 rounded text-[10px] font-semibold text-red-700 bg-red-50 hover:bg-red-100 transition-colors">
                                Xóa
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-center text-[11px] text-gray-500 bg-gray-50 p-2 rounded mb-2">
                <span>{{ $vps->ram ?? 1 }}GB RAM · {{ $vps->cpu ?? 1 }}vCPU</span>
                <span>Node: {{ $vps->cloudSunnyAccount->label ?? '—' }}</span>
            </div>
            <div class="flex justify-between items-center text-[11px] text-gray-500">
                <span>Tạo: <span class="font-semibold text-gray-700">{{ $vps->created_at->format('d/m/Y') }}</span></span>
                <span>Hết: <span class="font-semibold {{ $vps->expires_at && $vps->expires_at->isPast() ? 'text-red-600' : 'text-gray-700' }}">{{ $vps->expires_at ? $vps->expires_at->format('d/m/Y') : 'Vĩnh viễn' }}</span></span>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div>
    {{ $instances->links() }}
</div>

@push('scripts')
<script>

</script>
@endpush
@endsection
