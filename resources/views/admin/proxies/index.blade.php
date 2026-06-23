@extends('layouts.app')
@section('title', 'Quản lý Proxy — Admin')

@section('breadcrumbs')
    <span>Quản trị</span><span class="mx-2 text-gray-300">/</span><span class="text-gray-900">Proxy</span>
@endsection

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Proxy toàn hệ thống</h1>
    <p class="text-sm text-gray-500 mt-1">Tổng cộng: {{ $proxies->total() }} Proxy.</p>
</div>

<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50">
        <form method="GET" action="{{ route('admin.proxies.index') }}" class="flex flex-col sm:flex-row gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm IP, mã Proxy, username, người dùng..." class="w-full sm:w-96 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:border-cloud-600 focus:ring-1 focus:ring-cloud-600">
            <select name="status" class="px-3 py-2 border border-gray-300 rounded-md text-sm bg-white focus:outline-none focus:border-cloud-600">
                <option value="">Tất cả trạng thái</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ $status }}</option>
                @endforeach
            </select>
            <button class="px-4 py-2 border border-gray-300 rounded-md bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50">Lọc</button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[980px]">
            <thead><tr class="bg-gray-50 border-b border-gray-200">
                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Proxy</th>
                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Chủ sở hữu</th>
                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Kết nối</th>
                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Node</th>
                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Trạng thái</th>
                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Thời hạn</th>
                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase text-right">Đã thu</th>
                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase text-right">Thao tác</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($proxies as $proxy)
                    @php $proxyType = strtoupper($proxy->type_proxy ?? 'HTTP'); @endphp
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4"><div class="font-bold text-sm">{{ $proxy->id }} · {{ $proxyType }}</div><div class="text-xs text-gray-400">Provider #{{ $proxy->provider_proxy_id ?? '—' }}</div></td>
                        <td class="px-6 py-4">@if($proxy->user)<a href="{{ route('admin.users.show', $proxy->user) }}#proxies" class="font-bold text-sm text-gray-900 hover:text-cloud-600">{{ $proxy->user->name }}</a><div class="text-xs text-gray-500">{{ $proxy->user->email }}</div>@else<span class="text-gray-400">—</span>@endif</td>
                        <td class="px-6 py-4"><div class="font-mono text-sm">{{ $proxy->ip ?? 'Đang chờ IP...' }}</div><div class="text-xs text-gray-500">Port: {{ $proxyType === 'SOCKS5' ? ($proxy->sock5_port ?? '—') : ($proxy->port ?? '—') }}</div></td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ optional($proxy->cloudSunnyAccount)->label ?? '—' }}</td>
                        <td class="px-6 py-4"><span class="inline-flex px-2 py-0.5 rounded border text-xs font-semibold {{ $proxy->status === 'Hoạt động' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-yellow-50 text-yellow-700 border-yellow-200' }}">{{ $proxy->status }}</span></td>
                        <td class="px-6 py-4 text-sm {{ $proxy->expires_at && $proxy->expires_at->isPast() ? 'text-red-600 font-semibold' : 'text-gray-600' }}">{{ $proxy->expires_at ? $proxy->expires_at->format('d/m/Y H:i') : '—' }}</td>
                        <td class="px-6 py-4 text-right font-mono text-sm font-bold text-cloud-600">{{ number_format($proxy->paid_amount) }} đ</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('proxy.show', $proxy) }}" class="inline-flex px-3 py-1.5 border border-cloud-200 rounded text-xs font-semibold text-cloud-700 bg-cloud-50 hover:bg-cloud-100">Xem</a>
                                <form method="POST" action="{{ route('admin.proxies.destroy', $proxy) }}" data-confirm="Xóa Proxy #{{ $proxy->id }} của {{ optional($proxy->user)->name ?? 'người dùng' }}? Thao tác này không thể hoàn tác.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex px-3 py-1.5 border border-red-200 rounded text-xs font-semibold text-red-700 bg-red-50 hover:bg-red-100">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-6 py-12 text-center text-gray-500">Không tìm thấy Proxy.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $proxies->links() }}
@endsection
