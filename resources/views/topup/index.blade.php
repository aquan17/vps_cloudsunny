@extends('layouts.app')
@section('title', 'Nạp tiền & Thanh toán — NovaCloud')

@section('breadcrumbs')
    <span>Nạp tiền & Thanh toán</span>
@endsection

@section('content')
<div class="max-w-5xl mx-auto mb-10">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Thanh toán</h1>
            <p class="text-sm text-gray-500 mt-1">Quản lý số dư tài khoản và nạp thêm tiền.</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm px-5 py-3 flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-cloud-50 flex items-center justify-center text-cloud-600">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" /></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-0.5">Số dư hiện tại</p>
                <p class="text-xl font-extrabold text-gray-900 font-mono">{{ number_format(auth()->user()->balance) }} đ</p>
            </div>
        </div>
    </div>



    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- ════ MAIN CONTENT (Top Up Form & History) ════ --}}
        <div class="lg:col-span-2 space-y-8">
            
            {{-- Top Up Form --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <svg class="text-cloud-600" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Nạp tiền (Tự động)
                </h2>

                <form method="POST" action="{{ route('topup.store') }}">
                    @csrf

                    {{-- Quick amounts --}}
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Chọn nhanh</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach([50000, 100000, 200000, 500000, 1000000] as $amt)
                            <button type="button"
                                    onclick="setAmount({{ $amt }})"
                                    class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:border-cloud-500 hover:text-cloud-700 hover:bg-cloud-50 transition-colors">
                                {{ number_format($amt) }} đ
                            </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">Số tiền cần nạp (VNĐ)</label>
                        <div class="relative">
                            <input type="hidden" name="amount" id="amount" value="{{ old('amount', 100000) }}">
                            <input type="text" id="amount_display" inputmode="numeric"
                                   class="w-full pl-4 pr-12 py-2.5 border border-gray-300 rounded-md font-mono text-sm focus:outline-none focus:border-cloud-600 focus:ring-1 focus:ring-cloud-600 @error('amount') border-red-500 @enderror"
                                   value="{{ number_format(old('amount', 100000)) }}" required>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-medium text-sm">đ</span>
                            </div>
                        </div>
                        @error('amount')<p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                        <p class="mt-1.5 text-xs text-gray-500">Số tiền tối thiểu: 10,000 đ</p>
                    </div>

                    <button type="submit" class="w-full flex justify-center items-center gap-2 py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-cloud-600 hover:bg-cloud-700 transition-colors">
                        Tiếp tục thanh toán
                    </button>
                </form>
            </div>

            {{-- History --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center bg-gray-50/50">
                    <h2 class="text-base font-bold text-gray-900">Lịch sử giao dịch</h2>
                </div>

                @if($requests->isEmpty())
                    <div class="px-6 py-10 text-center">
                        <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3 text-gray-400">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900 mb-1">Chưa có giao dịch nào</h3>
                        <p class="text-sm text-gray-500">Các yêu cầu nạp tiền của bạn sẽ hiển thị ở đây.</p>
                    </div>
                @else
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Ngày tạo</th>
                                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Số tiền</th>
                                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Trạng thái</th>
                                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                            @foreach($requests as $r)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">{{ $r->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 font-mono">{{ number_format($r->amount) }} đ</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusClasses = [
                                            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                            'approved' => 'bg-green-100 text-green-800 border-green-200',
                                            'paid' => 'bg-green-100 text-green-800 border-green-200',
                                            'rejected' => 'bg-red-100 text-red-800 border-red-200',
                                        ];
                                        $statusClass = $statusClasses[$r->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold border {{ $statusClass }}">
                                        {{ $r->statusLabel() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-right">
                                    <a href="{{ route('topup.show', $r->id) }}" class="text-cloud-600 hover:text-cloud-800 font-semibold text-xs">Xem chi tiết</a>
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- MOBILE BOX VIEW --}}
                    <div class="block md:hidden divide-y divide-gray-100">
                        @foreach($requests as $r)
                        <div class="p-4 hover:bg-gray-50/50 transition-colors flex flex-col gap-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="block text-sm font-bold text-gray-900 font-mono">{{ number_format($r->amount) }} đ</span>
                                    <span class="block text-xs text-gray-500 font-mono mt-1">{{ $r->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'approved' => 'bg-green-100 text-green-800 border-green-200',
                                        'paid' => 'bg-green-100 text-green-800 border-green-200',
                                        'rejected' => 'bg-red-100 text-red-800 border-red-200',
                                    ];
                                    $statusClass = $statusClasses[$r->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold border {{ $statusClass }}">
                                    {{ $r->statusLabel() }}
                                </span>
                            </div>
                            <div class="pt-2 text-right">
                                <a href="{{ route('topup.show', $r->id) }}" class="inline-block text-cloud-600 hover:text-cloud-800 font-semibold text-xs px-3 py-1.5 border border-cloud-200 rounded bg-cloud-50">Xem chi tiết</a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $requests->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- ════ INSTRUCTIONS SIDEBAR ════ --}}
        <div class="lg:col-span-1">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 sticky top-24">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-5">Thanh toán tự động</h3>
                
                <div class="text-sm text-gray-600 space-y-4">
                    <p>
                        Chúng tôi xử lý thanh toán tự động qua <strong class="text-gray-900">PayOS</strong>.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-2">
                            <svg class="text-green-500 mt-0.5 flex-shrink-0" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            <span>Nhập số tiền bạn muốn nạp.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="text-green-500 mt-0.5 flex-shrink-0" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            <span>Quét mã QR bằng ứng dụng ngân hàng của bạn.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="text-green-500 mt-0.5 flex-shrink-0" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            <span>Số dư sẽ được cộng tự động trong vài giây!</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
function copyText(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const origText = btn.innerHTML;
        btn.innerHTML = 'Đã copy!';
        btn.classList.add('text-green-600');
        setTimeout(() => { 
            btn.innerHTML = origText; 
            btn.classList.remove('text-green-600');
        }, 2000);
    });
}

function setAmount(val) {
    document.getElementById('amount').value = val;
    document.getElementById('amount_display').value = new Intl.NumberFormat('en-US').format(val);
}

document.addEventListener('DOMContentLoaded', function() {
    const displayInput = document.getElementById('amount_display');
    const hiddenInput = document.getElementById('amount');

    if(displayInput && hiddenInput) {
        displayInput.addEventListener('input', function(e) {
            let val = this.value.replace(/\D/g, '');
            if (val === '') {
                hiddenInput.value = '';
                this.value = '';
                return;
            }
            hiddenInput.value = val;
            this.value = new Intl.NumberFormat('en-US').format(val);
        });
    }
});
</script>
@endpush
@endsection
