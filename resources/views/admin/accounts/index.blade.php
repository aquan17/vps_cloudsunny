@extends('layouts.app')
@section('title', 'SeaServer Accounts - Admin')

@section('breadcrumbs')
    <span>Quản trị</span>
    <span class="mx-2 text-gray-300">/</span>
    <span class="text-gray-900">SeaServer API</span>
@endsection

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">SeaServer Agency Accounts</h1>
    <p class="text-sm text-gray-500 mt-1">Quản lý API username, app key, secret key và số dư đại lý.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Đang hoạt động</p>
        <p class="text-2xl font-extrabold text-gray-900">{{ $stats['active'] }}</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Tạm khóa / full</p>
        <p class="text-2xl font-extrabold text-gray-900">{{ $stats['full'] }}</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Số dư đại lý</p>
        <p class="text-2xl font-extrabold text-gray-900">{{ number_format($stats['total_reserved']) }} đ</p>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
    <div class="xl:col-span-3">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between gap-4 bg-gray-50/50">
                <h2 class="text-base font-bold text-gray-900">Danh sách account</h2>
                <form method="POST" action="{{ route('admin.accounts.sync-all') }}">
                    @csrf
                    <button class="px-3 py-1.5 border border-gray-300 rounded-md text-xs font-semibold text-gray-700 bg-white hover:bg-gray-50">
                        Đồng bộ tất cả
                    </button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left min-w-[760px]">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Account</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Số dư</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Token</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase text-center">VPS</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Trạng thái</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($rows as $row)
                            @php $acc = $row['model']; @endphp
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 text-sm">{{ $acc->label }}</div>
                                    <div class="text-xs text-gray-500">{{ $acc->api_username }}</div>
                                    @if($acc->sync_error)
                                        <div class="text-[11px] text-red-600 mt-1">{{ $acc->sync_error }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-mono font-bold text-cloud-600">{{ number_format($acc->credit_vnd) }} đ</div>
                                    <div class="text-xs text-gray-500">Tổng nạp: {{ number_format($acc->total_vnd) }} đ</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($acc->token_expires_at)
                                        <div class="text-xs text-gray-700">{{ $acc->token_expires_at->format('d/m/Y H:i') }}</div>
                                    @else
                                        <span class="text-xs text-gray-400">Chưa có token</span>
                                    @endif
                                    <div class="text-[11px] text-gray-400">Sync: {{ $acc->last_synced_at ? $acc->last_synced_at->diffForHumans() : '-' }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="text-sm font-bold text-gray-900">{{ $acc->active_count ?? 0 }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if(!$acc->is_active)
                                        <span class="px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-600">Tắt</span>
                                    @elseif($acc->is_full)
                                        <span class="px-2 py-0.5 rounded text-xs font-semibold bg-red-50 text-red-700">Full</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-xs font-semibold bg-green-50 text-green-700">Active</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.accounts.edit', $acc) }}" class="px-2 py-1 border rounded text-xs text-gray-700 hover:bg-gray-50">Sửa</a>
                                        <form method="POST" action="{{ route('admin.accounts.sync', $acc) }}">
                                            @csrf
                                            <button class="px-2 py-1 border rounded text-xs text-gray-700 hover:bg-gray-50">Sync</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.accounts.toggle', $acc) }}">
                                            @csrf
                                            <input type="hidden" name="field" value="is_full">
                                            <button class="px-2 py-1 border rounded text-xs text-gray-700 hover:bg-gray-50">{{ $acc->is_full ? 'Mở' : 'Full' }}</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">Chưa có SeaServer account.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="xl:col-span-1 sticky top-6 self-start">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <h3 class="text-base font-bold text-gray-900 mb-5">Thêm SeaServer API</h3>
            <form method="POST" action="{{ route('admin.accounts.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Tên hiển thị</label>
                    <input type="text" name="label" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm" required value="{{ old('label') }}">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">API Username</label>
                    <input type="email" name="api_username" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm" required value="{{ old('api_username') }}">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">API App Key</label>
                    <input type="text" name="api_app" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm font-mono" required value="{{ old('api_app') }}">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">API Secret Key</label>
                    <input type="password" name="api_secret" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm font-mono" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Ưu tiên</label>
                    <input type="number" name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm" value="{{ old('priority', 0) }}">
                </div>
                <button type="submit" class="w-full py-2.5 px-4 rounded-md text-sm font-bold text-white bg-cloud-600 hover:bg-cloud-700">
                    Thêm và đồng bộ
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
