@extends('layouts.app')
@section('title', 'Thuê Proxy Mới - SeaServer')

@section('breadcrumbs')
    <a href="{{ route('proxy.index') }}" class="hover:text-gray-900 transition-colors">Quản lý Proxy</a>
    <span class="mx-2 text-gray-300">/</span>
    <span class="text-gray-900">Thuê Proxy Mới</span>
@endsection

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Thuê Proxy Mới</h1>
        <p class="text-sm text-gray-500 mt-1">Lựa chọn cấu hình và thuê Proxy tốc độ cao.</p>
    </div>
    <a href="{{ route('proxy.index') }}" class="inline-flex items-center gap-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2 px-4 rounded-md transition-colors text-sm shadow-sm">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Quản lý Proxy
    </a>
</div>

<form method="POST" action="{{ route('proxy.store.store') }}" id="proxyForm" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    @csrf

    <div class="lg:col-span-2 space-y-6">

    @if(session('success'))
        <div class="p-4 rounded-xl bg-green-50 border border-green-200 text-sm text-green-800 font-medium flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-sm text-red-800 font-medium flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- 1. CHỌN GÓI PROXY --}}
    <section class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-8 h-8 rounded-full bg-cloud-100 text-cloud-700 flex items-center justify-center font-bold text-sm">1</div>
            <h2 class="text-lg font-bold text-gray-900">Chọn gói Proxy</h2>
        </div>

        @if(empty($products))
            <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                <h3 class="text-base font-medium text-gray-900 mb-1">Chưa có cấu hình Proxy</h3>
                <p class="text-sm text-gray-500">Tài khoản CloudSunny hiện tại chưa cấu hình bán các gói Proxy. Vui lòng liên hệ Admin hoặc cấu hình trên hệ thống nhà cung cấp.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($products as $product)
                <label class="cursor-pointer group relative">
                    <input type="radio" name="product_id" value="{{ $product['id'] }}" class="peer sr-only" required
                           onchange="updatePrice()"
                           data-pricing="{{ json_encode($product['data_pricing'] ?? []) }}">
                    <div class="flex flex-col p-4 border-2 rounded-xl transition-all peer-checked:border-cloud-600 peer-checked:bg-cloud-50 peer-checked:ring-1 peer-checked:ring-cloud-600 border-gray-200 bg-white group-hover:border-cloud-300">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-base font-bold text-gray-900">{{ $product['title'] }}</span>
                        </div>
                        <div class="text-xl font-bold text-cloud-600 font-mono mb-3">
                            {{ number_format($product['data_pricing']['monthly'] ?? 0) }}đ<span class="text-xs text-gray-500 font-normal">/tháng</span>
                        </div>
                        <ul class="text-sm text-gray-600 space-y-1.5 flex-1">
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Loại: <span class="font-medium text-gray-900">{{ $categories[$product['category_id']] ?? 'Private' }}</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Băng thông: <span class="font-medium text-gray-900">{{ $product['bandwidth'] == 0 ? 'Không giới hạn' : $product['bandwidth'].' GB' }}</span>
                            </li>
                        </ul>
                    </div>
                </label>
                @endforeach
            </div>
        @endif
    </section>

    {{-- 2. CHU KỲ VÀ SỐ LƯỢNG --}}
    <section class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-8 h-8 rounded-full bg-cloud-100 text-cloud-700 flex items-center justify-center font-bold text-sm">2</div>
            <h2 class="text-lg font-bold text-gray-900">Thiết lập đơn hàng</h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Chu kỳ thanh toán</label>
                <div class="relative">
                    <select name="billing_cycle" id="billing_cycle" class="w-full pl-4 pr-10 py-3 border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-cloud-500 focus:border-cloud-500 text-sm font-medium transition-colors" onchange="updatePrice()">
                        @foreach($billingCycles as $key => $cycle)
                            <option value="{{ $key }}">{{ $cycle['label'] }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Số lượng</label>
                <input type="number" name="quantity" id="quantity" value="1" min="1" max="10" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cloud-500 focus:border-cloud-500 text-sm font-medium transition-colors" onchange="updatePrice()" oninput="updatePrice()">
            </div>
        </div>
    </section>

    {{-- 3. CẤU HÌNH XÁC THỰC --}}
    <section class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-8 h-8 rounded-full bg-cloud-100 text-cloud-700 flex items-center justify-center font-bold text-sm">3</div>
            <h2 class="text-lg font-bold text-gray-900">Xác thực kết nối <span class="text-sm font-normal text-gray-500 ml-2">(Tùy chọn)</span></h2>
        </div>
        
        <p class="text-sm text-gray-500 mb-6 bg-blue-50/50 p-4 rounded-lg border border-blue-100/50">
            <strong>Lưu ý:</strong> Quý khách có thể để trống toàn bộ nếu không có nhu cầu tùy chỉnh. Hệ thống sẽ tự động gán Port kết nối ngẫu nhiên để tăng tính bảo mật, kèm theo Username/Password mặc định hoặc kích hoạt tính năng nhận diện IP gốc.
        </p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-4">
                <h3 class="font-semibold text-gray-900 pb-2 border-b">Giao thức HTTP</h3>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Username <span class="text-gray-400 font-normal">(Tùy chọn)</span></label>
                    <input type="text" name="http_username" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-cloud-500 focus:border-cloud-500 text-sm font-mono" placeholder="Để trống = Random">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Password <span class="text-gray-400 font-normal">(Tùy chọn)</span></label>
                    <input type="text" name="http_password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-cloud-500 focus:border-cloud-500 text-sm font-mono" placeholder="Để trống = Random">
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="font-semibold text-gray-900 pb-2 border-b">Giao thức SOCKS5</h3>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Username <span class="text-gray-400 font-normal">(Tùy chọn)</span></label>
                    <input type="text" name="sock5_username" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-cloud-500 focus:border-cloud-500 text-sm font-mono" placeholder="Để trống = Random">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Password <span class="text-gray-400 font-normal">(Tùy chọn)</span></label>
                    <input type="text" name="sock5_password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-cloud-500 focus:border-cloud-500 text-sm font-mono" placeholder="Để trống = Random">
                </div>
            </div>
        </div>
    </section>
    </div>

    {{-- TOTAL & SUBMIT SIDEBAR --}}
    <div class="lg:col-span-1">
        <div class="sticky top-24 bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 pb-4 border-b">Tóm tắt đơn hàng</h2>
            
            <div class="space-y-4 mb-6">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Dịch vụ:</span>
                    <span class="font-medium text-gray-900">Thuê Proxy IPv4</span>
                </div>
                <div class="flex justify-between items-end border-t border-gray-100 pt-4 mt-2">
                    <span class="text-gray-900 font-bold">Tổng thanh toán:</span>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-cloud-600 font-mono tracking-tight leading-none" id="total_price_display">0đ</div>
                    </div>
                </div>
                <div class="flex justify-between text-xs text-gray-500 bg-gray-50 p-2 rounded">
                    <span>Số dư khả dụng:</span>
                    <span class="font-bold text-gray-900 font-mono">{{ number_format(Auth::user()->balance) }}đ</span>
                </div>
            </div>
            
            <button type="submit" id="submit_btn" class="w-full bg-cloud-600 hover:bg-cloud-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white px-6 py-3.5 rounded-xl font-bold shadow-md hover:shadow-lg transition-all text-base flex items-center justify-center gap-2" disabled>
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Thanh toán & Khởi tạo
            </button>
            <p class="text-[11px] text-gray-400 text-center mt-3 leading-relaxed">
                Bằng việc bấm nút Thanh toán, bạn đồng ý với Điều khoản dịch vụ và Chính sách bảo mật của chúng tôi.
            </p>
        </div>
    </div>
</form>

<script>
function updatePrice() {
    const checkedPlan = document.querySelector('input[name="product_id"]:checked');
    const cycle = document.getElementById('billing_cycle').value;
    const qty = parseInt(document.getElementById('quantity').value) || 1;
    const btn = document.getElementById('submit_btn');
    const display = document.getElementById('total_price_display');
    const userBalance = {{ Auth::user()->balance }};

    if (!checkedPlan) {
        display.innerText = '0 đ';
        btn.disabled = true;
        return;
    }

    try {
        const pricing = JSON.parse(checkedPlan.dataset.pricing);
        const pricePerCycle = pricing[cycle] || 0;
        
        if (pricePerCycle <= 0) {
            display.innerText = 'Gói không hỗ trợ chu kỳ này';
            btn.disabled = true;
            return;
        }

        const total = pricePerCycle * qty;
        display.innerText = new Intl.NumberFormat('vi-VN').format(total) + ' đ';

        if (total > userBalance) {
            btn.disabled = true;
            display.innerHTML += '<div class="text-red-500 text-sm mt-1 font-medium flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>Số dư không đủ</div>';
        } else {
            btn.disabled = false;
        }

    } catch(e) {
        console.error("Lỗi parse pricing", e);
    }
}

document.addEventListener('DOMContentLoaded', updatePrice);
</script>
@endsection
