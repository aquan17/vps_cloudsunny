@extends('layouts.app')
@section('title', $user->name . ' — Admin User')

@section('breadcrumbs')
    <span>Quản trị</span>
    <span class="mx-2 text-gray-300">/</span>
    <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-900 transition-colors">Người dùng</a>
    <span class="mx-2 text-gray-300">/</span>
    <span class="text-gray-900">{{ $user->name }}</span>
@endsection

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-gray-900 transition-colors">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
        Quay lại Danh sách
    </a>
</div>



<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{-- Left Column: User Info & Top up History --}}
    <div class="lg:col-span-2 space-y-8">
        
        {{-- User Info --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-5">Thông tin Tài khoản</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-5 gap-x-8">
                <div>
                    <span class="block text-xs text-gray-500 mb-1">Tên</span>
                    <span class="font-bold text-gray-900">{{ $user->name }}</span>
                </div>
                <div>
                    <span class="block text-xs text-gray-500 mb-1">Email</span>
                    <span class="font-mono text-sm text-gray-900">{{ $user->email }}</span>
                </div>
                <div>
                    <span class="block text-xs text-gray-500 mb-1">Số dư</span>
                    <span class="font-mono font-bold text-cloud-600 text-lg">{{ number_format($user->balance) }} đ</span>
                </div>
                <div>
                    <span class="block text-xs text-gray-500 mb-1">VPS</span>
                    <span class="font-bold text-gray-900">{{ $user->vps_instances_count }}</span>
                </div>
                <div>
                    <span class="block text-xs text-gray-500 mb-1">Proxy</span>
                    <span class="font-bold text-gray-900">{{ $user->proxy_instances_count }}</span>
                </div>
                <div>
                    <span class="block text-xs text-gray-500 mb-1">Vai trò</span>
                    @if($user->is_admin)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">Admin</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">User</span>
                    @endif
                </div>
                <div>
                    <span class="block text-xs text-gray-500 mb-1">Ngày đăng ký</span>
                    <span class="text-sm text-gray-900">{{ $user->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>

        {{-- Proxy List --}}
        <div id="proxies" class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50/50 flex items-center justify-between">
                <h2 class="text-base font-bold text-gray-900">Proxy của người dùng</h2>
                <span class="text-xs font-semibold text-gray-500">{{ $proxies->count() }} Proxy</span>
            </div>
            @if($proxies->isEmpty())
                <div class="px-6 py-8 text-center text-sm text-gray-500">Người dùng này chưa có Proxy.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[720px]">
                        <thead><tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Proxy</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Kết nối</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Trạng thái</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Hết hạn</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase text-right">Node</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($proxies as $proxy)
                                @php $proxyType = strtoupper($proxy->type_proxy ?? 'HTTP'); @endphp
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-6 py-4"><div class="font-bold text-sm text-gray-900">#{{ $proxy->id }} · {{ $proxyType }}</div><div class="text-xs text-gray-400">Provider #{{ $proxy->provider_proxy_id ?? '—' }}</div></td>
                                    <td class="px-6 py-4 font-mono text-sm text-gray-700">{{ $proxy->ip ? $proxy->ip.':'.($proxyType === 'SOCKS5' ? ($proxy->sock5_port ?? '—') : ($proxy->port ?? '—')) : 'Đang chờ IP...' }}</td>
                                    <td class="px-6 py-4"><span class="inline-flex px-2 py-0.5 rounded border text-xs font-semibold {{ $proxy->status === 'Hoạt động' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-yellow-50 text-yellow-700 border-yellow-200' }}">{{ $proxy->status }}</span></td>
                                    <td class="px-6 py-4 text-sm {{ $proxy->expires_at && $proxy->expires_at->isPast() ? 'text-red-600 font-semibold' : 'text-gray-600' }}">{{ $proxy->expires_at ? $proxy->expires_at->format('d/m/Y H:i') : '—' }}</td>
                                    <td class="px-6 py-4 text-right text-xs text-gray-500">{{ optional($proxy->cloudSunnyAccount)->label ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Top up History --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50/50">
                <h2 class="text-base font-bold text-gray-900">Lịch sử nạp tiền</h2>
            </div>
            
            @if($topups->isEmpty())
                <div class="px-6 py-8 text-center">
                    <p class="text-sm text-gray-500">Không có yêu cầu nạp tiền nào cho người dùng này.</p>
                </div>
            @else
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Ngày tạo</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Yêu cầu</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Số tiền duyệt</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                        @foreach($topups as $t)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                                {{ $t->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 font-mono">
                                {{ number_format($t->amount) }} đ
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-cloud-600 font-mono">
                                {{ $t->approved_amount ? number_format($t->approved_amount).' đ' : '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'approved' => 'bg-green-100 text-green-800 border-green-200',
                                        'rejected' => 'bg-red-100 text-red-800 border-red-200',
                                    ];
                                    $statusClass = $statusClasses[$t->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold border {{ $statusClass }}">
                                    {{ $t->statusLabel() }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- MOBILE BOX VIEW --}}
                <div class="block md:hidden divide-y divide-gray-100">
                    @foreach($topups as $t)
                    <div class="p-4 hover:bg-gray-50/50 transition-colors flex justify-between items-start">
                        <div>
                            <span class="block text-sm font-bold text-gray-900 font-mono">{{ number_format($t->amount) }} đ</span>
                            @if($t->approved_amount && $t->approved_amount != $t->amount)
                                <span class="block text-xs font-bold text-cloud-600 font-mono mt-1">Duyệt: {{ number_format($t->approved_amount) }} đ</span>
                            @endif
                            <span class="block text-xs text-gray-500 font-mono mt-1">{{ $t->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @php
                            $statusClasses = [
                                'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                'approved' => 'bg-green-100 text-green-800 border-green-200',
                                'rejected' => 'bg-red-100 text-red-800 border-red-200',
                            ];
                            $statusClass = $statusClasses[$t->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                        @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold border {{ $statusClass }}">
                            {{ $t->statusLabel() }}
                        </span>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Right Column: Actions Sidebar --}}
    <div class="lg:col-span-1 space-y-6">
        
        {{-- Adjust Balance --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 sticky top-24">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-5">Điều chỉnh số dư</h3>
            
            <form method="POST" action="{{ route('admin.users.balance', $user) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Số tiền (+ để cộng, - để trừ)</label>
                    <input type="number" name="amount" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm font-mono focus:outline-none focus:border-cloud-600 focus:ring-1 focus:ring-cloud-600"
                           placeholder="+100000 hoặc -50000" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Ghi chú (Tùy chọn)</label>
                    <input type="text" name="note" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:border-cloud-600 focus:ring-1 focus:ring-cloud-600"
                           placeholder="Lý do điều chỉnh">
                </div>
                <button type="submit" class="w-full flex justify-center items-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-bold text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    Cập nhật số dư
                </button>
            </form>
        </div>

        {{-- Toggle Admin --}}
        <div class="bg-amber-50 border border-amber-200 rounded-xl shadow-sm p-6">
            <h3 class="text-xs font-bold text-amber-800 uppercase tracking-wider mb-2">Quyền Quản trị</h3>
            <p class="text-sm text-amber-700 mb-4">
                {{ $user->is_admin ? 'Người dùng này hiện có quyền Admin.' : 'Người dùng này hiện là User bình thường.' }}
            </p>
            <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}"
                  data-confirm="{{ $user->is_admin ? 'Thu hồi quyền Admin của '.$user->name.'?' : 'Cấp quyền Admin cho '.$user->name.'?' }}">
                @csrf
                <button type="submit" class="w-full flex justify-center items-center py-2 px-4 border border-amber-300 rounded-md shadow-sm text-sm font-bold text-amber-800 bg-white hover:bg-amber-100 transition-colors">
                    {{ $user->is_admin ? 'Thu hồi quyền Admin' : 'Cấp quyền Admin' }}
                </button>
            </form>
        </div>

    </div>
</div>

@push('scripts')
<script>

</script>
@endpush
@endsection
