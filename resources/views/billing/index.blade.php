@extends('layouts.app')
@section('title', 'Lịch sử thanh toán — SeaServer')

@section('breadcrumbs')
    <span>Lịch sử thanh toán</span>
@endsection

@push('head')
<style>
/* ── Timeline ── */
.btl-wrap { position: relative; }
.btl-wrap::before {
    content: '';
    position: absolute;
    left: 19px; top: 0; bottom: 0; width: 2px;
    background: #e5e7eb;
}
.btl-item {
    position: relative;
    padding-left: 52px;
    padding-bottom: 1.25rem;
}
.btl-item:last-child { padding-bottom: 0; }

.btl-dot {
    position: absolute;
    left: 0; top: 2px;
    width: 40px; height: 40px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    border: 3px solid #f8fafc;
    z-index: 1;
}
.btl-dot.d-topup   { background: #dcfce7; color: #16a34a; }
.btl-dot.d-buy     { background: #fef9c3; color: #a16207; }
.btl-dot.d-renew   { background: #ede9fe; color: #7c3aed; }
.btl-dot.d-upgrade { background: #e0f2fe; color: #0284c7; }
.btl-dot.d-refund  { background: #fce7f3; color: #db2777; }
.btl-dot.d-spend   { background: #f3f4f6; color: #6b7280; }

/* ── Badge ── */
.txn-badge {
    display: inline-flex; align-items: center;
    padding: 2px 8px;
    border-radius: 999px;
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .04em;
    flex-shrink: 0;
}
.b-topup   { background: #dcfce7; color: #15803d; }
.b-buy     { background: #fef9c3; color: #92400e; }
.b-renew   { background: #ede9fe; color: #5b21b6; }
.b-upgrade { background: #e0f2fe; color: #0369a1; }
.b-refund  { background: #fce7f3; color: #9d174d; }
.b-spend   { background: #f3f4f6; color: #4b5563; }

/* ── Stat card ── */
.st-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 1.25rem 1.5rem;
    display: flex; align-items: center; gap: 1rem;
    transition: box-shadow .2s;
}
.st-card:hover { box-shadow: 0 4px 18px rgb(0 0 0/.07); }
.st-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}

/* ── Amount colors ── */
.amt-pos { color: #16a34a; }
.amt-neg { color: #374151; }

/* ── Month separator ── */
.month-label {
    padding-left: 52px;
    margin-bottom: 10px;
}
.month-label:not(:first-child) { margin-top: 20px; }
.month-label span {
    font-size: 11px; font-weight: 700;
    color: #9ca3af; text-transform: uppercase; letter-spacing: .06em;
}

/* ── Content card ── */
.btl-card {
    background: #f9fafb;
    border: 1px solid #f3f4f6;
    border-radius: 10px;
    padding: 10px 14px;
    transition: background .15s, border-color .15s, box-shadow .15s;
}
.btl-card:hover {
    background: #fff;
    border-color: #e5e7eb;
    box-shadow: 0 2px 8px rgb(0 0 0/.05);
}
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Lịch sử thanh toán</h1>
            <p class="text-sm text-gray-500 mt-1">Toàn bộ giao dịch nạp tiền và chi tiêu của bạn.</p>
        </div>
        <a href="{{ route('topup.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-cloud-600 hover:bg-cloud-700 text-white text-sm font-bold rounded-lg transition-colors shadow-sm">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Nạp tiền
        </a>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">

        {{-- Số dư ví --}}
        <div class="st-card">
            <div class="st-icon" style="background:#eff6ff">
                <svg width="22" height="22" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider" style="margin-bottom:2px">Số dư ví</p>
                <p class="text-xl font-extrabold text-gray-900" style="font-family:monospace">{{ number_format($currentBalance) }} đ</p>
            </div>
        </div>

        {{-- Tổng đã nạp --}}
        <div class="st-card">
            <div class="st-icon" style="background:#f0fdf4">
                <svg width="22" height="22" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.338-2.32 5.75 5.75 0 011.076 11.095" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider" style="margin-bottom:2px">Tổng đã nạp</p>
                <p class="text-xl font-extrabold" style="color:#15803d;font-family:monospace">+{{ number_format($totalTopup) }} đ</p>
            </div>
        </div>

        {{-- Tổng đã chi --}}
        <div class="st-card">
            <div class="st-icon" style="background:#fffbeb">
                <svg width="22" height="22" fill="none" stroke="#b45309" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider" style="margin-bottom:2px">Tổng đã chi</p>
                <p class="text-xl font-extrabold" style="color:#b45309;font-family:monospace">{{ number_format($totalSpend) }} đ</p>
            </div>
        </div>

    </div>

    {{-- Timeline card --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200" style="background:#f9fafb">
            <h2 class="text-base font-bold text-gray-900">Chi tiết giao dịch</h2>
            <p class="text-xs text-gray-500 mt-1">Tất cả hoạt động tài chính của bạn theo thứ tự thời gian.</p>
        </div>

        @if($timeline->isEmpty())
            <div class="px-6 py-16 text-center">
                <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4 text-gray-300">
                    <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-900 mb-1">Chưa có giao dịch nào</h3>
                <p class="text-sm text-gray-500 mb-4">Hãy nạp tiền để bắt đầu sử dụng dịch vụ.</p>
                <a href="{{ route('topup.index') }}"
                   class="inline-flex items-center gap-1 text-sm font-semibold text-cloud-600 hover:text-cloud-700">
                    Nạp tiền ngay
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>
        @else
            <div class="p-6">
                <div class="btl-wrap">
                    @php $prevMonth = null; @endphp
                    @foreach($timeline as $item)
                        @php
                            $itemMonth = $item['at'] ? $item['at']->format('m/Y') : null;
                            $isNewMonth = ($itemMonth !== $prevMonth);
                            $prevMonth  = $itemMonth;

                            $dotMap = [
                                'topup'   => 'd-topup',
                                'buy'     => 'd-buy',
                                'renew'   => 'd-renew',
                                'upgrade' => 'd-upgrade',
                                'refund'  => 'd-refund',
                            ];
                            $dotCls = $dotMap[$item['category']] ?? 'd-spend';

                            $badgeMap = [
                                'topup'   => 'b-topup',
                                'buy'     => 'b-buy',
                                'renew'   => 'b-renew',
                                'upgrade' => 'b-upgrade',
                                'refund'  => 'b-refund',
                            ];
                            $badgeCls = $badgeMap[$item['category']] ?? 'b-spend';

                            $labelMap = [
                                'topup'   => 'Nạp tiền',
                                'buy'     => 'Mua mới',
                                'renew'   => 'Gia hạn',
                                'upgrade' => 'Nâng cấp',
                                'refund'  => 'Hoàn tiền',
                            ];
                            $label = $labelMap[$item['category']] ?? 'Chi tiêu';
                            $isPos = $item['amount'] > 0;
                        @endphp

                        @if($isNewMonth && $itemMonth)
                            <div class="month-label">
                                <span>Tháng {{ $itemMonth }}</span>
                            </div>
                        @endif

                        <div class="btl-item">
                            {{-- Dot icon --}}
                            <div class="btl-dot {{ $dotCls }}">
                                @if($isPos)
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3" />
                                    </svg>
                                @elseif($item['category'] === 'renew')
                                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                    </svg>
                                @else
                                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                                    </svg>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="btl-card">
                                <div class="flex items-start justify-between gap-3">
                                    <div style="flex:1;min-width:0">
                                        <div class="flex items-center gap-2 flex-wrap" style="margin-bottom:4px">
                                            <span class="txn-badge {{ $badgeCls }}">{{ $label }}</span>
                                            <span class="text-sm font-semibold text-gray-800" style="line-height:1.3">
                                                {{ $item['description'] }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-3 flex-wrap">
                                            <span class="text-xs text-gray-400" style="font-family:monospace;display:flex;align-items:center;gap:4px">
                                                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ $item['at'] ? $item['at']->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') : '—' }}
                                            </span>
                                            @if($item['ref'])
                                                <span class="text-xs text-gray-400" style="font-family:monospace">{{ $item['ref'] }}</span>
                                            @endif
                                            @if($item['category'] === 'topup' && isset($item['meta']->id))
                                                <a href="{{ route('topup.show', $item['meta']->id) }}"
                                                   class="text-xs font-semibold text-cloud-600 hover:text-cloud-700">
                                                    Xem chi tiết →
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    <div style="text-align:right;flex-shrink:0">
                                        <span class="text-base font-extrabold {{ $isPos ? 'amt-pos' : 'amt-neg' }}" style="font-family:monospace">
                                            {{ $isPos ? '+' : '' }}{{ number_format($item['amount']) }} đ
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Back link --}}
    <div class="mt-6 text-center">
        <a href="{{ route('topup.index') }}"
           class="inline-flex items-center gap-1 text-sm font-semibold text-cloud-600 hover:text-cloud-700">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
            Quay lại trang nạp tiền
        </a>
    </div>

</div>
@endsection
