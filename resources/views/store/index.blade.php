@extends('layouts.app')
@section('title', 'Bảng giá — SeaServer')

@section('breadcrumbs')
    <span>Khởi tạo VPS</span>
@endsection

@section('content')

{{-- ── Pricing Hero ── --}}
<div class="text-center max-w-3xl mx-auto mb-12">
    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-cloud-50 text-cloud-700 border border-cloud-100 mb-4 tracking-wide uppercase">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        Hệ thống bởi SeaServer
    </span>
    <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight mb-4">
        Bảng giá đơn giản, minh bạch.
    </h1>
    <p class="text-lg text-gray-500">
        Máy chủ SSD Linux hiệu năng cao đáp ứng mọi nhu cầu. Khởi tạo chỉ trong vài giây.
    </p>

    {{-- Feature pills --}}
    <div class="flex justify-center gap-2 flex-wrap mt-6">
        @foreach([
            ['⚡', 'Khởi tạo tự động'],
            // ['🌏', 'Singapore · Tokyo · Jakarta'],
            ['🔒', 'Ubuntu 22.04 LTS'],
            ['💳', 'Thanh toán bằng VNĐ'],
        ] as [$icon, $label])
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white border border-gray-200 text-sm font-medium text-gray-600 shadow-sm">
            {{ $icon }} {{ $label }}
        </span>
        @endforeach
    </div>

    {{-- Slot availability summary --}}
    @auth
    <div class="mt-6 flex items-center justify-center gap-2 flex-wrap bg-white border border-gray-200 rounded-lg p-3 shadow-sm inline-flex">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider mr-2">Trạng thái hệ thống:</span>
        @foreach($plans as $pid => $p)
        @php $ok = in_array($pid, $availablePlanIds ?? []); @endphp
        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-xs font-semibold
            {{ $ok ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-gray-50 text-gray-500 border border-gray-200' }}">
            <span class="w-1.5 h-1.5 rounded-full {{ $ok ? 'bg-green-500' : 'bg-gray-400' }}"></span>
            {{ $p['name'] }}
        </span>
        @endforeach
    </div>
    @endauth
</div>

{{-- Plans Grid --}}
<div class="mb-12">
    @include('partials.plans-grid', ['plans' => $plans, 'order' => true])
</div>

<p class="text-center mt-12 text-sm text-gray-400">
    Giá đã bao gồm hạ tầng mạng · Thanh toán trả trước hàng tháng · Không tự động gia hạn
</p>

@endsection
