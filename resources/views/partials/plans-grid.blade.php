{{--
    plans-grid.blade.php
    Variables:
      $plans  — array tu CloudSunnyPricingService
      $order  — bool: hiển thị nút "Đặt mua" hay không
--}}

@php
$featuredIds    = ['nova-pro'];
$availablePlans = $availablePlanIds ?? [];
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    @foreach($plans as $id => $plan)
    @php
        $isFeatured  = in_array($id, $featuredIds);
        $badge       = ['label' => $plan['badge'] ?? null, 'color' => 'gray'];
        $hasSlot     = in_array($id, $availablePlans);
    @endphp

    <article class="bg-white rounded-xl shadow-sm border {{ $isFeatured ? 'border-cloud-500 ring-1 ring-cloud-500' : 'border-gray-200 hover:border-cloud-300' }} transition-all flex flex-col relative overflow-hidden">
        
        {{-- Optional top highlight bar --}}
        @if($isFeatured)
            <div class="absolute top-0 left-0 right-0 h-1 bg-cloud-500"></div>
        @endif
        
        <div class="p-6 flex-1 flex flex-col">
            {{-- Header --}}
            <div class="flex justify-between items-start mb-2">
                <h3 class="text-lg font-bold text-gray-900">{{ $plan['name'] }}</h3>
                @if(!empty($badge['label']))
                    @php
                        $color = $badge['color'] ?: 'gray';
                        $colorClasses = [
                            'gray' => 'bg-gray-100 text-gray-600',
                            'blue' => 'bg-blue-100 text-blue-700',
                            'purple' => 'bg-purple-100 text-purple-700',
                            'amber' => 'bg-amber-100 text-amber-700',
                            'green' => 'bg-green-100 text-green-700',
                            'rose' => 'bg-rose-100 text-rose-700',
                        ][$color] ?? 'bg-gray-100 text-gray-600';
                    @endphp
                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $colorClasses }}">
                        {{ $badge['label'] }}
                    </span>
                @endif
            </div>
            
            <p class="text-sm text-gray-500 mb-6 flex-1">{{ $plan['desc'] }}</p>

            {{-- Price --}}
            <div class="mb-6 pb-6 border-b border-gray-100">
                <div class="flex items-end gap-1">
                    @php $displayPrice = (!empty($plan['on_sale'])) ? $plan['sale_price_per_month'] : $plan['price_per_month']; @endphp
                    <span class="text-3xl font-extrabold text-gray-900">{{ number_format($displayPrice) }}</span>
                    @if(!empty($plan['on_sale']))
                        <span class="ml-2 text-sm text-gray-500 line-through">{{ number_format($plan['price_per_month']) }}</span>
                    @endif
                    <span class="text-sm font-medium text-gray-500 mb-1">đ/tháng</span>
                </div>
            </div>

            {{-- Specs --}}
            <ul class="space-y-4 mb-8">
                <li class="flex items-center gap-3 text-sm text-gray-600">
                    <svg class="text-gray-400" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" /></svg>
                    <span class="font-semibold text-gray-900">{{ $plan['cores'] }}</span> vCPU
                </li>
                <li class="flex items-center gap-3 text-sm text-gray-600">
                    <svg class="text-gray-400" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-16.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a4.5 4.5 0 01.9-2.7L5.737 5.1a3.375 3.375 0 012.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 01.9 2.7m0 0a3 3 0 01-3 3m0 3h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008zm-3 6h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008z" /></svg>
                    <span class="font-semibold text-gray-900">{{ $plan['ram'] }} GB</span> RAM
                </li>
                <li class="flex items-center gap-3 text-sm text-gray-600">
                    <svg class="text-gray-400" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25-4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" /></svg>
                    <span class="font-semibold text-gray-900">{{ $plan['disk'] }} GB</span> NVMe SSD
                </li>
                <li class="flex items-center justify-between text-sm text-gray-600">
                    <div class="flex items-center gap-3">
                        <svg class="text-gray-400" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" /></svg>
                        <span>Băng thông</span>
                    </div>
                    <span class="font-semibold text-gray-900">{{ ($plan['transfer_tb'] ?? 1) === 'Unlimited' ? 'Không giới hạn' : ($plan['transfer_tb'] ?? 1) . ' TB' }}</span>
                </li>
                <li class="flex items-center justify-between text-sm text-gray-600">
                    <div class="flex items-center gap-3">
                        <svg class="text-gray-400" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" /></svg>
                        <span>Tốc độ mạng</span>
                    </div>
                    @php $netSpeed = $plan['network_out_mbps'] ?? 100; @endphp
                    <span class="font-semibold text-gray-900">{{ $netSpeed >= 1000 ? ($netSpeed / 1000) . ' Gbps' : $netSpeed . ' Mbps' }}</span>
                </li>
            </ul>

            {{-- CTA --}}
            @if(!empty($order) && auth()->check())
                @if($hasSlot)
                    <a href="{{ route('store.create', $id) }}" class="mt-auto w-full flex justify-center items-center py-2.5 px-4 border rounded-md shadow-sm text-sm font-medium transition-colors
                        {{ $isFeatured ? 'border-transparent text-white bg-cloud-600 hover:bg-cloud-700' : 'border-gray-300 text-gray-700 bg-white hover:bg-gray-50' }}">
                        Đặt mua
                    </a>
                @else
                    <button disabled class="mt-auto w-full flex justify-center items-center py-2.5 px-4 border border-gray-200 rounded-md bg-gray-50 text-sm font-medium text-gray-400 cursor-not-allowed">
                        Tạm hết slot
                    </button>
                @endif
            @else
                <a href="{{ route('login') }}" class="mt-auto w-full flex justify-center items-center py-2.5 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    Đăng nhập để Mua
                </a>
            @endif
        </div>
    </article>
    @endforeach
</div>
