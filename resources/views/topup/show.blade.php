@extends('layouts.app')
@section('title', 'Thanh toán — SeaServer')
@section('robots', 'noindex, nofollow')

@section('breadcrumbs')
    <span>Nạp tiền & Thanh toán</span>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-900">Thanh toán</span>
@endsection

@section('content')
    @php
        $payosData = (array) data_get($order->raw_payload, 'data', []);
        $payosCheckoutUrl = $payosData['checkoutUrl'] ?? null;
        $payosQrCode = $payosData['qrCode'] ?? null;
        $qrImage = $payosQrCode
            ? 'https://api.qrserver.com/v1/create-qr-code/?size=320x320&data=' . urlencode($payosQrCode)
            : $order->viet_qr_url;
    @endphp

    <div class="max-w-4xl mx-auto mb-10" id="depositPaymentPage" data-status-url="{{ route('topup.status', $order->id) }}" data-is-pending="{{ $order->status === 'pending' ? '1' : '0' }}">
        
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Hoàn tất thanh toán</h1>
                <p class="text-sm text-gray-500 mt-1">Quét mã QR để hoàn tất thanh toán. Số dư sẽ được cộng tự động.</p>
            </div>
            <a href="{{ route('topup.index') }}" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                Quay lại
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            {{-- QR Code Section --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-8 flex flex-col items-center justify-center text-center">
                @if($order->status === 'paid' || $order->status === 'approved')
                    <div class="w-24 h-24 rounded-full bg-green-100 flex items-center justify-center text-green-600 mb-6 shadow-sm border border-green-200">
                        <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Thanh toán thành công</h2>
                    <p class="mt-3 text-sm text-gray-500 max-w-xs mx-auto leading-relaxed">
                        Số dư của bạn đã được cập nhật. Bạn có thể sử dụng số dư này để khởi tạo máy chủ mới.
                    </p>
                    <div class="mt-8 w-full gap-3 flex flex-col">
                        <a href="{{ route('topup.index') }}" class="w-full py-2.5 bg-cloud-600 text-white rounded-lg text-sm font-bold hover:bg-cloud-700 transition-colors">
                            Quay lại Nạp tiền
                        </a>
                        <a href="{{ route('dashboard') }}" class="w-full py-2.5 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-bold hover:bg-gray-50 transition-colors">
                            Đến Dashboard
                        </a>
                    </div>
                @else
                    <div class="p-3 bg-white border border-gray-200 rounded-2xl shadow-sm inline-block">
                        <img src="{{ $qrImage }}" alt="QR Code" class="w-64 h-64 rounded-xl">
                    </div>
                    <p class="mt-6 text-sm text-gray-500 font-medium">Quét bằng ứng dụng ngân hàng</p>

                    @if($payosCheckoutUrl)
                        <a href="{{ $payosCheckoutUrl }}" target="_blank" rel="noopener" class="mt-6 w-full py-2.5 bg-gray-900 text-white rounded-lg text-sm font-bold hover:bg-black transition-colors flex items-center justify-center gap-2">
                            Mở cổng thanh toán PayOS
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
                        </a>
                    @endif
                @endif
            </div>

            {{-- Payment Details Section --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm flex flex-col">
                <div class="p-6 border-b border-gray-200 bg-gray-50/50 rounded-t-xl">
                    <h3 class="text-base font-bold text-gray-900">Thông tin thanh toán</h3>
                    <p class="text-xs text-gray-500 mt-1">Vui lòng không thay đổi số tiền hoặc nội dung chuyển khoản.</p>
                </div>
                
                <div class="p-6 flex-1 flex flex-col justify-center divide-y divide-gray-100">
                    <div class="py-3 flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-500">Ngân hàng</span>
                        <strong class="text-sm font-bold text-gray-900">{{ $payosData['bin'] ?? config('deposit.bank_id') }}</strong>
                    </div>
                    <div class="py-3 flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-500">Số tài khoản</span>
                        <strong class="text-sm font-bold font-mono text-gray-900 tracking-wide">{{ $payosData['accountNumber'] ?? config('deposit.account_no') }}</strong>
                    </div>
                    <div class="py-3 flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-500">Tên tài khoản</span>
                        <strong class="text-sm font-bold text-gray-900 uppercase">{{ $payosData['accountName'] ?? config('deposit.account_name') }}</strong>
                    </div>
                    <div class="py-3 flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-500">Số tiền</span>
                        <strong class="text-base font-extrabold font-mono text-cloud-600">{{ number_format($order->amount) }} VNĐ</strong>
                    </div>
                    <div class="py-3 flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-500">Nội dung chuyển khoản</span>
                        <strong class="text-base font-extrabold font-mono text-red-600 tracking-widest">{{ $order->code }}</strong>
                    </div>
                    <div class="py-3 flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-500">Trạng thái</span>
                        <span id="depositStatusBadge" class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wider {{ $order->status === 'paid' || $order->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $order->status === 'paid' || $order->status === 'approved' ? 'ĐÃ THANH TOÁN' : 'ĐANG CHỜ THANH TOÁN' }}
                        </span>
                    </div>
                </div>

                <div class="p-6 border-t border-gray-200">
                    <div id="depositLiveMessage" class="rounded-lg border p-4 text-sm font-medium {{ $order->status === 'paid' || $order->status === 'approved' ? 'border-green-200 bg-green-50 text-green-800' : 'border-cloud-200 bg-cloud-50 text-cloud-800' }}">
                        @if($order->status === 'paid' || $order->status === 'approved')
                            Đã xác nhận thanh toán. Số dư của bạn đã được cập nhật.
                        @else
                            <div class="flex items-center gap-2">
                                <svg class="animate-spin text-cloud-500" width="16" height="16" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Đang chờ thanh toán... Chúng tôi sẽ tự động cập nhật.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        var page = document.getElementById('depositPaymentPage');
        if (!page || page.dataset.isPending !== '1') return;

        var statusUrl = page.dataset.statusUrl;
        var badge = document.getElementById('depositStatusBadge');
        var message = document.getElementById('depositLiveMessage');
        var attempts = 0;
        var maxAttempts = 120; // 6 minutes total at 3s interval

        function markPaid(data) {
            page.dataset.isPending = '0';

            // Just reload the page to show the success UI completely
            window.location.reload();
        }

        function pollStatus() {
            attempts++;

            fetch(statusUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (data && data.paid) {
                    markPaid(data);
                    return;
                }

                if (attempts < maxAttempts) {
                    setTimeout(pollStatus, 3000);
                }
            })
            .catch(function () {
                if (attempts < maxAttempts) {
                    setTimeout(pollStatus, 5000);
                }
            });
        }

        setTimeout(pollStatus, 3000);
    })();
</script>
@endpush
