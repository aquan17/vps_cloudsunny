@extends('layouts.app')

@section('content')
<div class="w-full py-2">
    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 leading-tight">Chi tiết Proxy {{ $proxy->id }}</h1>
                @if($proxy->status === 'Hoạt động')
                    <span class="inline-flex shrink-0 items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200" id="status-badge">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                        Hoạt động
                    </span>
                @else
                    <span class="inline-flex shrink-0 items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-200" id="status-badge">
                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 animate-pulse"></span>
                        <span id="status-text">{{ $proxy->status }}</span>
                    </span>
                @endif
            </div>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Thông tin chi tiết và thao tác quản lý</p>
        </div>
        <a href="{{ auth()->user()->isAdmin() ? route('admin.proxies.index') : route('proxy.index') }}" class="inline-flex w-fit items-center text-cloud-600 hover:text-cloud-700 font-medium text-sm">
            &larr; Quay lại danh sách
        </a>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        
        {{-- THÔNG TIN KẾT NỐI --}}
        <div class="xl:col-span-3 space-y-6">
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
                                <div class="flex max-w-md">
                                    <input type="text" readonly value="{{ $proxy->ip }}" class="w-full font-mono text-sm text-gray-900 bg-white px-3 py-2 border border-r-0 rounded-l">
                                    <button type="button" class="px-3 py-2 bg-white border rounded-r text-gray-500 hover:text-gray-900 hover:bg-gray-50" data-copy-value="{{ $proxy->ip }}" title="Sao chép IP">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    </button>
                                </div>
                            </div>
                            
                            @php $typeProxy = strtoupper($proxy->type_proxy ?? 'HTTP'); @endphp

                            @if($typeProxy === 'SOCKS5')
                                <div class="col-span-2 bg-gray-50 rounded-lg p-4 border border-gray-100">
                                    <h4 class="font-semibold text-gray-700 mb-3 text-sm border-b pb-2">Giao thức SOCKS5</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
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
                            @else
                                <div class="col-span-2 bg-gray-50 rounded-lg p-4 border border-gray-100">
                                    <h4 class="font-semibold text-gray-700 mb-3 text-sm border-b pb-2">Giao thức HTTP</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1">Cổng (Port)</label>
                                            <div class="flex">
                                                <input type="text" readonly value="{{ $proxy->port ?? '-' }}" class="w-full font-mono text-sm text-gray-900 bg-white px-2 py-1 border border-r-0 rounded-l">
                                                <button type="button" class="px-2 py-1 bg-white border rounded-r text-gray-500 hover:text-gray-900 hover:bg-gray-50" data-copy-value="{{ $proxy->port ?? '' }}" title="Sao chép Port">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                </button>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1">Username</label>
                                            <div class="flex">
                                                <input type="text" readonly value="{{ $proxy->username ?: 'Không yêu cầu' }}" class="w-full font-mono text-sm text-gray-900 bg-white px-2 py-1 border border-r-0 rounded-l">
                                                <button type="button" class="px-2 py-1 bg-white border rounded-r text-gray-500 hover:text-gray-900 hover:bg-gray-50" data-copy-value="{{ $proxy->username ?? '' }}" title="Sao chép Username">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                </button>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1">Password</label>
                                            <div class="flex">
                                                <input type="text" readonly value="{{ $proxy->password ?: 'Không yêu cầu' }}" class="w-full font-mono text-sm text-gray-900 bg-white px-2 py-1 border border-r-0 rounded-l">
                                                <button type="button" class="px-2 py-1 bg-white border rounded-r text-gray-500 hover:text-gray-900 hover:bg-gray-50" data-copy-value="{{ $proxy->password ?? '' }}" title="Sao chép Password">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            
            {{-- <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
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
            </div> --}}

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
                        <div class="font-medium text-gray-900">{{ $proxy->product_id }}</div>
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

                    @if($proxy->user_id === auth()->id())
                    <form action="{{ route('proxy.renew', $proxy->id) }}" method="POST" class="pt-2">
                        @csrf
                        <input type="hidden" name="billing_cycle" value="monthly">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gia hạn thêm</label>
                        <div class="mb-3 flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm">
                            <span class="font-medium text-gray-900">{{ $billingCycles['monthly']['label'] ?? '1 Tháng' }}</span>
                            <span class="text-xs text-gray-500">Chu kỳ cố định</span>
                        </div>
                        <button type="submit" class="w-full bg-cloud-600 hover:bg-cloud-700 text-white font-medium py-2.5 px-4 rounded-lg transition-colors text-sm shadow-sm" onclick="return confirm('Xác nhận thanh toán để gia hạn Proxy?');">
                            Thanh toán gia hạn
                        </button>
                    </form>
                    @endif

                    <form action="{{ auth()->user()->isAdmin() ? route('admin.proxies.destroy', $proxy) : route('proxy.destroy', $proxy) }}" method="POST" class="pt-2" data-confirm="Xoá Proxy này? Hành động này không thể hoàn tác.">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-medium text-red-700 shadow-sm transition-colors hover:bg-red-100">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                            Xoá Proxy
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

<script>
    document.querySelectorAll('[data-copy-value]').forEach((button) => {
        button.addEventListener('click', async () => {
            const value = button.dataset.copyValue || '';
            const original = button.innerHTML;

            try {
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(value);
                } else {
                    const textarea = document.createElement('textarea');
                    textarea.value = value;
                    textarea.style.position = 'fixed';
                    textarea.style.opacity = '0';
                    document.body.appendChild(textarea);
                    textarea.select();
                    document.execCommand('copy');
                    textarea.remove();
                }

                button.innerHTML = '<svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                setTimeout(() => {
                    button.innerHTML = original;
                }, 1200);
            } catch (error) {
                console.error(error);
                button.innerHTML = '<span class="text-xs text-red-600">Lỗi</span>';
                setTimeout(() => {
                    button.innerHTML = original;
                }, 1200);
            }
        });
    });
</script>

@endsection
