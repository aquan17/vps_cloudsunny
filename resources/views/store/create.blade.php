@extends('layouts.app')
@section('title', 'Tạo VPS ' . $plan['name'] . ' - SeaServer')

@section('breadcrumbs')
    <a href="{{ route('pricing') }}" class="hover:text-gray-900 transition-colors">Bảng giá</a>
    <span class="mx-2 text-gray-300">/</span>
    <span class="text-gray-900">Tạo VPS</span>
@endsection

@section('content')
@php
    $displayPrice = (!empty($plan['on_sale'])) ? $plan['sale_price_per_month'] : $plan['price_per_month'];
    $firstImage = array_key_first($images) ?: config('cloudsunny.default_os_id');
    $firstCycle = old('duration', array_key_first($durations) ?: config('cloudsunny.default_billing_cycle'));
    $prices = collect($durations)->mapWithKeys(function($label, $cycle) use ($plan) {
        $svc = app(\App\Services\CloudSunnyPricingService::class);
        return [$cycle => $svc->calculatePrice($plan, $cycle)];
    });
    $addonPrices = config('cloudsunny.addon_prices', []);
@endphp

<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Tạo VPS mới</h1>
    <p class="text-sm text-gray-500 mt-1">Cấu hình máy chủ ảo <span class="font-semibold text-cloud-600">{{ $plan['name'] }}</span>.</p>
</div>

<div id="fullScreenLoader" class="fixed inset-0 z-50 flex items-center justify-center bg-white/80 backdrop-blur-sm hidden">
    <div class="text-center bg-white p-8 rounded-xl shadow-2xl border border-gray-100 max-w-sm w-full mx-4">
        <div class="w-16 h-16 mx-auto mb-5 rounded-full border-4 border-cloud-100 border-t-cloud-600 animate-spin"></div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Đang tạo VPS...</h3>
        <p class="text-sm text-gray-500">Vui lòng không đóng trình duyệt trong lúc hệ thống gửi đơn sang SeaServer.</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2">
        <form method="POST" action="{{ route('store.store') }}" id="orderForm" class="space-y-6">
            @csrf
            <input type="hidden" name="plan" value="{{ $planId }}">
            <input type="hidden" name="image" id="selectedImage" value="{{ old('image', $firstImage) }}" required>

            @php
                $emailPrefix = explode('@', auth()->user()->email)[0] ?? 'vps';
                $cleanPrefix = preg_replace('/[^a-zA-Z0-9\-]/', '', $emailPrefix);
                $defaultLabel = $cleanPrefix . '-' . substr(uniqid(), -4);
            @endphp

            <section class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 rounded-full bg-cloud-100 text-cloud-700 flex items-center justify-center font-bold text-sm">1</div>
                    <h2 class="text-lg font-bold text-gray-900">Tên gợi nhớ</h2>
                </div>
                <input type="text" name="label"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-md font-mono text-sm"
                       value="{{ old('label', $defaultLabel) }}"
                       pattern="[a-zA-Z0-9\-]+" maxlength="32" required>
                <p class="mt-2 text-xs text-gray-500">Chỉ dùng chữ, số và dấu gạch ngang. Tối đa 32 ký tự.</p>
            </section>

            <section class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 rounded-full bg-cloud-100 text-cloud-700 flex items-center justify-center font-bold text-sm">2</div>
                    <h2 class="text-lg font-bold text-gray-900">Hệ điều hành</h2>
                </div>

                @php
                    $groups = [];
                    foreach ($images as $id => $img) {
                        $groups[$img['group']][$id] = $img;
                    }
                    $defaultImage = (int) old('image', $firstImage);
                @endphp

                <div class="space-y-6">
                    @foreach($groups as $groupName => $groupImages)
                        <div>
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">{{ $groupName }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($groupImages as $id => $img)
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="_image_radio" value="{{ $id }}"
                                               {{ (int) $id === $defaultImage ? 'checked' : '' }}
                                               class="peer sr-only"
                                               onchange="selectOs(this)">
                                        <div class="flex items-center gap-3 p-3 border rounded-lg transition-all peer-checked:border-cloud-600 peer-checked:bg-cloud-50 peer-checked:ring-1 peer-checked:ring-cloud-600 border-gray-200 bg-white group-hover:border-cloud-300">
                                            @php
                                                $icon = $img['icon'] ?? '';
                                                $icon = $icon === 'windows' ? '🪟' : ($icon === 'linux' ? '🐧' : $icon);
                                            @endphp
                                            <span class="text-xl w-8 text-center flex-shrink-0">{{ $icon }}</span>
                                            <span class="text-sm font-semibold text-gray-700">{{ $img['label'] }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 rounded-full bg-cloud-100 text-cloud-700 flex items-center justify-center font-bold text-sm">3</div>
                    <h2 class="text-lg font-bold text-gray-900">Khu vực</h2>
                </div>
                <input type="hidden" name="region" value="vn">
                <div class="flex items-center gap-3 w-full px-4 py-3 border border-gray-200 rounded-md bg-gray-50 text-sm">
                    <span class="text-lg">🇻🇳</span>
                    <span class="font-semibold text-gray-900">Việt Nam</span>
                </div>
            </section>

            <section class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 rounded-full bg-cloud-100 text-cloud-700 flex items-center justify-center font-bold text-sm">4</div>
                    <h2 class="text-lg font-bold text-gray-900">Thời gian thuê</h2>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($durations as $cycle => $label)
                        @php
                            $discount = config("cloudsunny.billing_cycles.{$cycle}.discount_percent", 0);
                        @endphp
                        <label class="cursor-pointer group relative">
                            <input type="radio" name="duration" value="{{ $cycle }}"
                                   {{ $cycle === $firstCycle ? 'checked' : '' }}
                                   class="peer sr-only"
                                   data-label="{{ $label }}"
                                   data-price="{{ $prices[$cycle] }}">
                            <div class="flex flex-col items-center justify-center p-4 border rounded-lg transition-all text-center peer-checked:border-cloud-600 peer-checked:bg-cloud-50 peer-checked:ring-1 peer-checked:ring-cloud-600 border-gray-200 bg-white group-hover:border-cloud-300">
                                <span class="text-sm font-bold text-gray-900 mb-1">{{ $label }}</span>
                                <span class="text-xs text-cloud-600 font-mono">{{ number_format($prices[$cycle]) }} đ</span>
                                @if($discount > 0)
                                    <div class="absolute -top-2 -right-2 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded shadow-sm border border-white">
                                        -{{ $discount }}%
                                    </div>
                                @endif
                            </div>
                        </label>
                    @endforeach
                </div>

                <div class="mt-8 border-t border-gray-100 pt-6">
                    <h3 class="text-sm font-bold text-gray-900 mb-4">Nâng cấp cấu hình (Tuỳ chọn)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">Thêm CPU (+{{ number_format($addonPrices['cpu'] ?? 22000) }}đ/Core/Tháng)</label>
                            <div class="relative">
                                <input type="number" name="addon_cpu" id="addon_cpu" min="0" max="16" step="1" value="{{ old('addon_cpu', 0) }}" class="w-full pl-3 pr-12 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-cloud-500 focus:border-cloud-500 text-sm transition-colors bg-white" oninput="updateTotalPrice()" onchange="updateTotalPrice()">
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span class="text-gray-500 text-xs">Core</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">Thêm RAM (+{{ number_format($addonPrices['ram'] ?? 22000) }}đ/GB/Tháng)</label>
                            <div class="relative">
                                <input type="number" name="addon_ram" id="addon_ram" min="0" max="64" step="1" value="{{ old('addon_ram', 0) }}" class="w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-cloud-500 focus:border-cloud-500 text-sm transition-colors bg-white" oninput="updateTotalPrice()" onchange="updateTotalPrice()">
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span class="text-gray-500 text-xs">GB</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">Thêm Ổ cứng (+{{ number_format($addonPrices['disk'] ?? 10000) }}đ/10GB/Tháng)</label>
                            <div class="relative">
                                <input type="number" name="addon_disk" id="addon_disk" min="0" step="10" value="{{ old('addon_disk', 0) }}" class="w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-cloud-500 focus:border-cloud-500 text-sm transition-colors bg-white" oninput="updateTotalPrice()" onchange="this.value = Math.max(0, Math.round((this.value || 0) / 10) * 10); updateTotalPrice()">
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span class="text-gray-500 text-xs">GB</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            @if($errors->any())
                <div class="p-4 rounded-md bg-red-50 border border-red-200 text-sm text-red-800 font-medium">
                    {{ $errors->first() }}
                </div>
            @endif
        </form>
    </div>

    <aside class="lg:col-span-1 sticky top-6 self-start">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-6">Tóm tắt đơn hàng</h3>

            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                <div class="font-bold text-gray-900">{{ $plan['name'] }}</div>
                <div class="text-xs text-gray-500 mt-1 mb-3">{{ $plan['desc'] }}</div>
                <div class="flex flex-wrap gap-2">
                    <span class="px-2 py-1 rounded bg-white border border-gray-200 text-[11px] font-medium text-gray-600"><span id="summaryCpu">{{ $plan['cores'] }}</span> vCPU</span>
                    <span class="px-2 py-1 rounded bg-white border border-gray-200 text-[11px] font-medium text-gray-600"><span id="summaryRam">{{ $plan['ram'] }}</span> GB RAM</span>
                    <span class="px-2 py-1 rounded bg-white border border-gray-200 text-[11px] font-medium text-gray-600"><span id="summaryDisk">{{ $plan['disk'] }}</span> GB SSD</span>
                </div>
            </div>

            <div class="space-y-3 mb-6">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">Hệ điều hành</span>
                    <span id="summaryOs" class="font-medium text-gray-900 text-right">{{ $images[$firstImage]['label'] ?? $firstImage }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">Thời gian</span>
                    <span id="summaryDuration" class="font-medium text-gray-900 text-right">{{ $durations[$firstCycle] ?? $firstCycle }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">Số dư hiện tại</span>
                    <span class="font-mono text-gray-900 text-right">{{ number_format(auth()->user()->balance) }} đ</span>
                </div>
            </div>

            <hr class="border-gray-200 mb-6">

            <div class="flex justify-between items-center mb-6">
                <span class="font-bold text-gray-900">Tổng thanh toán</span>
                <span id="totalPrice" class="text-xl font-extrabold text-cloud-600 font-mono">{{ number_format($prices[$firstCycle] ?? 0) }} đ</span>
            </div>

            @php $enough = auth()->user()->balance >= ($prices[$firstCycle] ?? 0); @endphp
            <button type="submit" form="orderForm" id="btnSubmitOrder" class="w-full py-3 px-4 rounded-lg shadow-sm text-sm font-bold text-white bg-cloud-600 hover:bg-cloud-700 {{ !$enough ? 'opacity-50 cursor-not-allowed' : '' }}" {{ !$enough ? 'disabled' : '' }}>
                Tạo VPS ngay
            </button>
            <div id="balanceErrorDiv" class="mt-4 p-3 rounded-md bg-red-50 border border-red-100" style="display: {{ !$enough ? 'block' : 'none' }}">
                <p class="text-xs text-red-700 text-center font-medium">Số dư không đủ.</p>
            </div>
        </div>
    </aside>
</div>
@endsection

@push('scripts')
<script>
const prices = @json($prices);
const addonPrices = @json($addonPrices);
const baseSpec = @json(['cpu' => $plan['cores'], 'ram' => $plan['ram'], 'disk' => $plan['disk']]);
const cycleMonths = @json(collect(config('cloudsunny.billing_cycles', []))->mapWithKeys(fn($v, $k) => [$k => $v['months'] ?? 1]));
const userBalance = {{ auth()->user()->balance }};
const images = @json(collect($images)->mapWithKeys(fn($v, $k) => [$k => $v['label']]));
let selectedCycle = @json($firstCycle);

function selectOs(radio) {
    document.getElementById('selectedImage').value = radio.value;
    document.getElementById('summaryOs').textContent = images[radio.value] || radio.value;
}

document.querySelectorAll('input[name="duration"]').forEach(radio => {
    radio.addEventListener('change', function() {
        selectedCycle = this.value;
        document.getElementById('summaryDuration').textContent = this.dataset.label || this.value;
        updateTotal();
    });
});

function updateTotal() {
    const months = Number(cycleMonths[selectedCycle] || 1);
    let total = Number(prices[selectedCycle] || 0);

    const addonCpu = parseInt(document.getElementById('addon_cpu').value) || 0;
    const addonRam = parseInt(document.getElementById('addon_ram').value) || 0;
    const addonDisk = parseInt(document.getElementById('addon_disk').value) || 0;

    let addonPrice = 0;
    if (months > 0) {
        let chargeFactor = 1.0;
        if (months === 0.5) {
             chargeFactor = 0.5;
        } else {
             chargeFactor = months;
        }
        
        addonPrice += addonCpu * (addonPrices.cpu || 22000) * chargeFactor;
        addonPrice += addonRam * (addonPrices.ram || 22000) * chargeFactor;
        addonPrice += (addonDisk / 10) * (addonPrices.disk || 10000) * chargeFactor;
    }

    total += addonPrice;

    document.getElementById('summaryCpu').textContent = baseSpec.cpu + addonCpu;
    document.getElementById('summaryRam').textContent = baseSpec.ram + addonRam;
    document.getElementById('summaryDisk').textContent = baseSpec.disk + addonDisk;

    document.getElementById('totalPrice').textContent = total.toLocaleString('vi-VN') + ' đ';
    
    const btnSubmit = document.getElementById('btnSubmitOrder');
    const errDiv = document.getElementById('balanceErrorDiv');
    if (total > userBalance) {
        btnSubmit.disabled = true;
        btnSubmit.classList.add('opacity-50', 'cursor-not-allowed');
        errDiv.style.display = 'block';
    } else {
        btnSubmit.disabled = false;
        btnSubmit.classList.remove('opacity-50', 'cursor-not-allowed');
        errDiv.style.display = 'none';
    }
}

function updateTotalPrice() {
    updateTotal();
}

document.getElementById('orderForm').addEventListener('submit', function() {
    document.getElementById('fullScreenLoader').classList.remove('hidden');
});

// Run once to initialize
updateTotal();
</script>
@endpush
