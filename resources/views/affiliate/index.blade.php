@extends('layouts.app')
@section('title', 'Cộng tác viên (Affiliate) — SeaServer')

@section('breadcrumbs')
    <span>Tiếp thị liên kết</span>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-900">Tổng quan</span>
@endsection

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Chương trình Affiliate</h1>
    <p class="text-sm text-gray-500 mt-1">Giới thiệu khách hàng mới và nhận hoa hồng 5% trọn đời.</p>
</div>

{{-- Stats Row --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    {{-- Total Commission --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 flex items-center">
        <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center text-green-600 mr-4">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Tổng hoa hồng nhận được</p>
            <p class="text-2xl font-extrabold text-green-600">{{ number_format($totalCommission) }} đ</p>
        </div>
    </div>

    {{-- Referral Count --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 flex items-center">
        <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 mr-4">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Số lượng khách giới thiệu</p>
            <p class="text-2xl font-extrabold text-gray-900">{{ number_format($referralCount) }} người (F1)</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-1 space-y-8">
        {{-- Affiliate Info --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-900">Mã & Link giới thiệu</h2>
            </div>
            <div class="p-6 space-y-4">
                <p class="text-sm text-gray-500 mb-2">Giới thiệu bạn bè đăng ký và nhận ngay <strong class="text-cloud-600">5% hoa hồng</strong> trọn đời cho mỗi giao dịch của họ.</p>
                <div>
                    <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Mã giới thiệu</span>
                    <div class="flex items-center space-x-3">
                        <input type="text" readonly value="{{ $user->ref_code }}" class="flex-1 block w-full px-4 py-2 rounded-md border border-gray-300 bg-gray-50 text-gray-900 text-sm font-mono outline-none" id="refCodeInput">
                        <button type="button" onclick="copyToClipboard('refCodeInput')" class="inline-flex items-center px-5 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-cloud-600 hover:bg-cloud-700 focus:outline-none">
                            Copy
                        </button>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Đường dẫn chia sẻ</span>
                    <div class="flex items-center space-x-3">
                        <input type="text" readonly value="{{ url('/register?ref=' . $user->ref_code) }}" class="flex-1 block w-full px-4 py-2 rounded-md border border-gray-300 bg-gray-50 text-gray-900 text-sm font-mono outline-none" id="refUrlInput">
                        <button type="button" onclick="copyToClipboard('refUrlInput')" class="inline-flex items-center px-5 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-cloud-600 hover:bg-cloud-700 focus:outline-none">
                            Copy Link
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50">
                <h2 class="text-lg font-bold text-gray-900">Lịch sử nhận hoa hồng</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người mua</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giao dịch</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Giá trị</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Hoa hồng (5%)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($logs as $log)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $log->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                {{ substr($log->buyer->email, 0, 4) }}***@gmail.com
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($log->transaction_type == 'buy_vps') Mua mới VPS
                                @elseif($log->transaction_type == 'renew_vps') Gia hạn VPS
                                @elseif($log->transaction_type == 'upgrade_vps') Nâng cấp VPS
                                @elseif($log->transaction_type == 'renew_proxy') Gia hạn Proxy
                                @else {{ $log->transaction_type }} @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                {{ number_format($log->amount) }} đ
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600 text-right">
                                +{{ number_format($log->commission) }} đ
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 text-sm">
                                Bạn chưa có giao dịch hoa hồng nào. Hãy chia sẻ link giới thiệu ngay nhé!
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function copyToClipboard(inputId) {
        var copyText = document.getElementById(inputId);
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");
        alert("Đã copy: " + copyText.value);
    }
</script>
@endsection
