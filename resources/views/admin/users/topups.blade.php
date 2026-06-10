@extends('layouts.app')
@section('title', 'Yêu cầu Nạp tiền — Admin')

@section('breadcrumbs')
    <span>Quản trị</span>
    <span class="mx-2 text-gray-300">/</span>
    <span class="text-gray-900">Yêu cầu Nạp tiền</span>
@endsection

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Yêu cầu Nạp tiền</h1>
        <p class="text-sm text-gray-500 mt-1">Xem xét và quản lý các yêu cầu nạp tiền vào số dư của người dùng.</p>
    </div>
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-colors">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
        Quay lại Danh sách
    </a>
</div>



{{-- Filters --}}
<div class="mb-6 flex gap-2 flex-wrap">
    @foreach(['all' => 'Tất cả', 'pending' => 'Đang chờ', 'approved' => 'Đã duyệt', 'rejected' => 'Từ chối'] as $val => $label)
    @php
        $isActive = request('status', 'all') === ($val === 'all' ? 'all' : $val);
    @endphp
    <a href="{{ route('admin.topups.index', $val !== 'all' ? ['status' => $val] : []) }}"
       class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors border {{ $isActive ? 'bg-cloud-50 text-cloud-700 border-cloud-200' : 'bg-white text-gray-600 border-gray-200 hover:border-cloud-300' }}">
        {{ $label }}
    </a>
    @endforeach
</div>

<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden flex flex-col mb-6">
    <div class="hidden lg:block overflow-x-auto flex-1">
        <table class="w-full text-left border-collapse min-w-[900px]">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Ngày tạo</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Người dùng</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Yêu cầu</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Ghi chú</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($topups as $t)
            <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                    {{ $t->created_at->format('d/m/Y H:i') }}
                </td>
                <td class="px-6 py-4">
                    <a href="{{ route('admin.users.show', $t->user_id) }}" class="font-bold text-gray-900 hover:text-cloud-600 transition-colors block mb-0.5">
                        {{ $t->user->name }}
                    </a>
                    <div class="text-xs text-gray-500">{{ $t->user->email }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-bold text-gray-900 font-mono">{{ number_format($t->amount) }} đ</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500 max-w-[200px] truncate" title="{{ $t->note ?? '—' }}">
                    {{ $t->note ?? '—' }}
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
                    @if($t->approved_amount && $t->approved_amount != $t->amount)
                        <div class="text-[11px] font-semibold text-green-600 mt-1">
                            ≈ {{ number_format($t->approved_amount) }} đ
                        </div>
                    @endif
                </td>
                <td class="px-6 py-4">
                    @if($t->isPending())
                        <div class="flex flex-col gap-2 min-w-[160px]">
                            <form method="POST" action="{{ route('admin.topups.approve', $t) }}" class="flex gap-2">
                                @csrf
                                <input type="number" name="approved_amount"
                                       class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs font-mono focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500"
                                       value="{{ $t->amount }}" min="1000">
                                <button type="submit" class="flex-shrink-0 px-3 py-1.5 bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 rounded text-xs font-bold transition-colors">
                                    Duyệt
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.topups.reject', $t) }}"
                                  data-confirm="Từ chối yêu cầu nạp {{ number_format($t->amount) }} đ từ {{ $t->user->name }}?">
                                @csrf
                                <button type="submit" class="w-full px-3 py-1.5 bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 rounded text-xs font-bold transition-colors">
                                    Từ chối
                                </button>
                            </form>
                        </div>
                    @else
                        <span class="text-xs text-gray-400 font-mono block">
                            {{ $t->processed_at ? $t->processed_at->format('d/m H:i') : '—' }}
                        </span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" /></svg>
                    <p>Không có yêu cầu nạp tiền nào.</p>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- MOBILE BOX VIEW --}}
    <div class="block lg:hidden divide-y divide-gray-100">
        @foreach($topups as $t)
        <div class="p-4 hover:bg-gray-50/50 transition-colors">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <a href="{{ route('admin.users.show', $t->user_id) }}" class="font-bold text-gray-900 hover:text-cloud-600 transition-colors text-sm mb-0.5 block">
                        {{ $t->user->name }}
                    </a>
                    <div class="text-xs text-gray-500 font-mono">{{ $t->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div class="text-right">
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
            </div>

            <div class="mb-3 p-3 bg-gray-50 rounded-lg border border-gray-100">
                <div class="flex justify-between items-center mb-1">
                    <span class="text-xs text-gray-500">Số tiền:</span>
                    <span class="font-bold text-gray-900 font-mono text-sm">{{ number_format($t->amount) }} đ</span>
                </div>
                @if($t->approved_amount && $t->approved_amount != $t->amount)
                <div class="flex justify-between items-center mb-1">
                    <span class="text-xs text-gray-500">Đã duyệt:</span>
                    <span class="font-semibold text-green-600 font-mono text-[11px]">{{ number_format($t->approved_amount) }} đ</span>
                </div>
                @endif
                @if($t->note)
                <div class="mt-2 text-xs text-gray-500 italic border-t border-gray-200 pt-2">
                    "{{ $t->note }}"
                </div>
                @endif
            </div>

            @if($t->isPending())
                <div class="flex flex-col gap-2 mt-3">
                    <form method="POST" action="{{ route('admin.topups.approve', $t) }}" class="flex gap-2">
                        @csrf
                        <input type="number" name="approved_amount"
                               class="w-full px-3 py-2 border border-gray-300 rounded text-sm font-mono focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500"
                               value="{{ $t->amount }}" min="1000">
                        <button type="submit" class="flex-shrink-0 px-4 py-2 bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 rounded text-sm font-bold transition-colors">
                            Duyệt
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.topups.reject', $t) }}"
                          data-confirm="Từ chối yêu cầu nạp {{ number_format($t->amount) }} đ từ {{ $t->user->name }}?">
                        @csrf
                        <button type="submit" class="w-full px-3 py-2 bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 rounded text-sm font-bold transition-colors">
                            Từ chối
                        </button>
                    </form>
                </div>
            @else
                <div class="text-right text-[10px] text-gray-400 mt-2">
                    Xử lý: {{ $t->processed_at ? $t->processed_at->format('d/m H:i') : '—' }}
                </div>
            @endif
        </div>
        @endforeach
    </div>
</div>

<div>
    {{ $topups->links() }}
</div>

@push('scripts')
<script>

</script>
@endpush
@endsection
