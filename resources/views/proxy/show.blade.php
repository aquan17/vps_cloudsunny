@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900">Chi tiết Proxy #{{ $proxy->id }}</h1>
                @if(strtolower($proxy->status) == 'hoạt động')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200" id="status-badge">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                        Hoạt động
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-200" id="status-badge">
                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 animate-pulse"></span>
                        <span id="status-text">{{ $proxy->status }}</span>
                    </span>
                @endif
            </div>
            <p class="text-sm text-gray-500 mt-1">Thông tin chi tiết và thao tác quản lý</p>
        </div>
        <a href="{{ route('proxy.index') }}" class="text-cloud-600 hover:text-cloud-700 font-medium text-sm">
            &larr; Quay lại danh sách
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- THÔNG TIN KẾT NỐI --}}
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                        Thông tin kết nối
                    </h3>
                </div>
                <div class="p-6">
                    @if(!$proxy->ip)
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-10 h-10 mx-auto text-gray-300 mb-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p>Đang cấp phát IP từ nhà cung cấp...</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-4">
                            <div class="col-span-2">
                                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Địa chỉ IP</label>
                                <div class="text-lg font-bold text-gray-900">{{ $proxy->ip }}</div>
                            </div>
                            
                            {{-- HTTP --}}
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                                <h4 class="font-semibold text-gray-700 mb-3 text-sm border-b pb-2">Giao thức HTTP</h4>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Cổng (Port)</label>
                                        <div class="font-mono text-sm text-gray-900 bg-white px-2 py-1 border rounded">{{ $proxy->port ?? '-' }}</div>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Username</label>
                                        <div class="font-mono text-sm text-gray-900 bg-white px-2 py-1 border rounded">{{ $proxy->username ?: 'Không yêu cầu' }}</div>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Password</label>
                                        <div class="font-mono text-sm text-gray-900 bg-white px-2 py-1 border rounded">{{ $proxy->password ?: 'Không yêu cầu' }}</div>
                                    </div>
                                </div>
                            </div>

                            {{-- SOCKS5 --}}
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                                <h4 class="font-semibold text-gray-700 mb-3 text-sm border-b pb-2">Giao thức SOCKS5</h4>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Cổng (Port)</label>
                                        <div class="font-mono text-sm text-gray-900 bg-white px-2 py-1 border rounded">{{ $proxy->sock5_port ?? '-' }}</div>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Username</label>
                                        <div class="font-mono text-sm text-gray-900 bg-white px-2 py-1 border rounded">{{ $proxy->sock5_username ?: 'Không yêu cầu' }}</div>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Password</label>
                                        <div class="font-mono text-sm text-gray-900 bg-white px-2 py-1 border rounded">{{ $proxy->sock5_password ?: 'Không yêu cầu' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            Tính năng đổi mật khẩu Proxy hiện đang được tích hợp thêm. Nếu cần đổi mật khẩu hoặc IP Auth gấp, vui lòng liên hệ hỗ trợ.
                        </p>
                    </div>
                </div>
            </div>

        </div>

        {{-- THÔNG TIN GÓI & GIA HẠN --}}
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Thông tin thanh toán
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Mã sản phẩm</label>
                        <div class="font-medium text-gray-900">#{{ $proxy->product_id }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Chu kỳ hiện tại</label>
                        <div class="font-medium text-gray-900 uppercase">{{ $proxy->billing_cycle }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Ngày hết hạn</label>
                        <div class="font-medium {{ \Carbon\Carbon::parse($proxy->expires_at)->isPast() ? 'text-red-600' : 'text-gray-900' }}">
                            {{ \Carbon\Carbon::parse($proxy->expires_at)->format('d/m/Y H:i') }}
                        </div>
                    </div>
                    
                    <hr class="border-gray-100">

                    <form action="{{ route('proxy.renew', $proxy->id) }}" method="POST" class="pt-2">
                        @csrf
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gia hạn thêm:</label>
                        <select name="billing_cycle" class="w-full rounded-md border-gray-300 shadow-sm focus:border-cloud-500 focus:ring-cloud-500 text-sm mb-3">
                            @foreach($billingCycles as $key => $cycle)
                                <option value="{{ $key }}" {{ $key == $proxy->billing_cycle ? 'selected' : '' }}>{{ $cycle['label'] }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="w-full bg-cloud-600 hover:bg-cloud-700 text-white font-medium py-2 px-4 rounded-md transition-colors text-sm" onclick="return confirm('Xác nhận thanh toán để gia hạn Proxy?');">
                            Thanh toán gia hạn
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@if(!$proxy->ip)
<script>
    // Polling trạng thái Proxy nếu chưa có IP
    setInterval(function() {
        fetch('{{ route("proxy.statusJson", $proxy->id) }}')
            .then(res => res.json())
            .then(data => {
                if(data.ip) {
                    window.location.reload();
                }
            })
            .catch(err => console.error(err));
    }, 5000);
</script>
@endif

@endsection
