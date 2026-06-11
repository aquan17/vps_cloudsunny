@extends('layouts.app')
@section('title', $vps->label . ' — NovaCloud')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="hover:text-gray-900 transition-colors">Máy chủ VPS</a>
    <span class="mx-2 text-gray-300">/</span>
    <span class="text-gray-900">{{ $vps->label }}</span>
@endsection

@section('content')
{{-- Header Area --}}
<div class="mb-8 flex flex-col md:flex-row md:items-start justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">{{ $vps->label }}</h1>
        <div class="flex items-center gap-3 mt-3 text-sm">
            @php
                $statusLower = mb_strtolower($vps->status, 'UTF-8');
                $isOk = str_contains($statusLower, 'running') || str_contains($statusLower, 'hoạt động') || str_contains($statusLower, 'sẵn sàng');
                $isErr = str_contains($statusLower, 'lỗi') || str_contains($statusLower, 'offline') || str_contains($statusLower, 'đã tắt');
                $osLabel = $vps->provider_payload['os'] ?? config("cloudsunny.images.{$vps->provider_os_id}.label", 'Ubuntu / Linux');
            @endphp
            <span id="vps-badge" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $isOk ? 'bg-green-50 text-green-700 border border-green-200' : ($isErr ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-yellow-50 text-yellow-700 border border-yellow-200') }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $isOk ? 'bg-green-500' : ($isErr ? 'bg-red-500' : 'bg-yellow-500') }}"></span>
                {{ $vps->status }}
            </span>
            <span class="text-gray-500">{{ $vps->region ?? 'Không rõ' }}</span>
            <span class="text-gray-300">•</span>
            <span class="text-gray-500">{{ $osLabel }}</span>
            
            <span id="polling-indicator" style="display:none;" class="items-center gap-1.5 text-cloud-600 font-medium ml-2">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="animate-spin"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                Đang đồng bộ...
            </span>
        </div>
    </div>
    <div class="flex gap-3">
        <form method="POST" action="{{ route('dashboard.sync', $vps) }}">
            @csrf
            <button type="submit" class="px-4 py-2 border border-gray-300 rounded-md bg-white text-gray-700 hover:bg-gray-50 font-medium text-sm transition-colors shadow-sm flex items-center gap-2">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                Đồng bộ trạng thái
            </button>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    {{-- Left Main Column --}}
    <div class="lg:col-span-2 space-y-6">
        
        {{-- Instance Specs Card --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <h2 class="text-base font-semibold text-gray-900 mb-6">Thông số cấu hình</h2>
            
            @php
                $planInfo = app(\App\Services\CloudSunnyPricingService::class)->getPlan($vps->plan_id) ?? config("cloudsunny.plans.{$vps->plan_id}");
                $transferTb = $planInfo['transfer_tb'] ?? 1;
                $transferStr = $transferTb === 'Unlimited' ? 'Không giới hạn' : $transferTb . ' TB';
                $networkOut = $planInfo['network_out_mbps'] ?? 100;
                $networkStr = $networkOut >= 1000 ? ($networkOut / 1000) . ' Gbps' : $networkOut . ' Mbps';
            @endphp
            <div class="grid grid-cols-2 md:grid-cols-5 gap-6 pb-6 border-b border-gray-100">
                <div>
                    <div class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">vCPU</div>
                    <div class="text-lg font-medium text-gray-900">{{ $vps->cpu ?? 1 }} Cores</div>
                </div>
                <div>
                    <div class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Bộ nhớ (RAM)</div>
                    <div class="text-lg font-medium text-gray-900">{{ $vps->ram ?? 1 }} GB RAM</div>
                </div>
                <div>
                    <div class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Ổ cứng</div>
                    <div class="text-lg font-medium text-gray-900">{{ $vps->disk ?? 25 }} GB NVMe</div>
                </div>
                <div>
                    <div class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Băng thông</div>
                    <div class="text-lg font-medium text-gray-900">{{ $transferStr }}</div>
                </div>
                <div>
                    <div class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Mạng In / Out</div>
                    <div class="text-sm font-medium text-gray-900 mt-1">{{ $networkStr }}</div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6">
                <div>
                    <div class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Địa chỉ IPv4</div>
                    <div class="flex items-center gap-2">
                        <span id="vps-ip" class="font-mono text-gray-900">{{ $vps->public_ip ?? 'Đang chờ...' }}</span>
                        @if($vps->public_ip)
                        <button onclick="copyText('{{ $vps->public_ip }}', this)" class="text-gray-400 hover:text-cloud-600 transition-colors">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" /></svg>
                        </button>
                        @endif
                    </div>
                </div>
                <div>
                    <div class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Thời gian</div>
                    <div class="text-sm">
                        <div class="mb-1 text-gray-500">Tạo: <span class="font-medium text-gray-900">{{ $vps->created_at->format('d/m/Y H:i') }}</span></div>
                        <div class="text-gray-500">Hết hạn: <span class="font-medium {{ $vps->expires_at && $vps->expires_at->isPast() ? 'text-red-600' : 'text-gray-900' }}">{{ $vps->expires_at ? $vps->expires_at->format('d/m/Y H:i') : 'Vĩnh viễn' }}</span></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Access Configuration Card (Dark Mode) --}}
        <div class="bg-[#111827] border border-gray-800 rounded-xl shadow-md p-6 relative overflow-hidden">
            <!-- Subtle gradient overlay -->
            <div class="absolute inset-0 bg-gradient-to-br from-cloud-600/5 to-transparent pointer-events-none"></div>
            
            <div class="flex items-center gap-2 mb-6">
                <svg class="text-gray-400" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                <h2 class="text-sm font-semibold text-gray-200">Thông tin truy cập</h2>
                <div class="ml-auto flex gap-1">
                    <div class="w-1.5 h-1.5 rounded-full bg-gray-600"></div><div class="w-1.5 h-1.5 rounded-full bg-gray-600"></div><div class="w-1.5 h-1.5 rounded-full bg-gray-600"></div>
                </div>
            </div>
            
            <div class="space-y-5 relative z-10">
                <div>
                    <div class="flex justify-between items-end mb-2">
                        <label class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                            @if($isWindows) Kết nối RDP (Remote Desktop) @else Lệnh SSH @endif
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 7.5l3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0021 18V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v12a2.25 2.25 0 002.25 2.25z" /></svg>
                        </label>
                    </div>
                    <div class="bg-[#1f2937] border border-gray-700 rounded-md p-3 flex justify-between items-center group">
                        <div class="relative flex-1 flex items-center min-h-[20px]">
                            <code id="ssh-cmd" class="font-mono text-sm text-gray-200">@if($isWindows){{ $vps->public_ip ? 'Administrator' : 'Đang chờ...' }}@else{{ $vps->public_ip ? 'ssh root@' . $vps->public_ip : 'Đang chờ...' }}@endif</code>
                        </div>
                        <button onclick="copyText(document.getElementById('ssh-cmd').innerText, this)" class="text-gray-500 hover:text-gray-300 opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" /></svg>
                        </button>
                    </div>
                </div>
                
                <div>
                    <label class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                        @if($isWindows) Mật khẩu Administrator @else Mật khẩu Root @endif
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" /></svg>
                    </label>
                    <div class="bg-[#1f2937] border border-gray-700 rounded-md p-3 flex justify-between items-center group cursor-pointer"
                         onmouseenter="document.getElementById('root-pass').style.filter='none'; document.getElementById('pass-hint').style.display='none'"
                         onmouseleave="document.getElementById('root-pass').style.filter='blur(5px)'; document.getElementById('pass-hint').style.display='block'">
                        <div class="relative flex-1 flex items-center min-h-[20px]">
                            <code id="root-pass" class="font-mono text-sm text-gray-200 transition-all duration-300" style="filter:blur(4px)">{{ $vps->root_password }}</code>
                            <span id="pass-hint" class="absolute inset-0 flex items-center">
                                <span class="bg-[#111827] px-2 py-0.5 rounded text-xs text-gray-400 font-medium">Di chuột để xem</span>
                            </span>
                        </div>
                        <button onclick="copyText('{{ $vps->root_password }}', this)" class="text-gray-500 hover:text-gray-300 opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" /></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Danger Zone --}}
        <div class="bg-red-50/30 border border-red-200 rounded-xl p-6 relative overflow-hidden mt-8">
            <div class="absolute top-0 left-0 w-full h-1 bg-red-500"></div>
            
            <div class="flex items-center gap-2 mb-2">
                <svg class="text-red-600" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                <h2 class="text-lg font-bold text-red-700">Khu vực nguy hiểm</h2>
            </div>
            
            <p class="text-sm text-gray-600 mb-6">Các thao tác ở đây sẽ phá hủy dữ liệu và không thể khôi phục dễ dàng. Cần hết sức cẩn thận.</p>
            
            <div class="flex flex-col sm:flex-row gap-4">
                <form method="POST" action="{{ route('dashboard.rebuild', $vps) }}" class="flex-1" data-confirm="⚠️ REBUILD sẽ XÓA TOÀN BỘ DỮ LIỆU. Xác nhận?">
                    @csrf
                    <button type="submit" class="w-full flex justify-center items-center gap-2 px-4 py-2.5 border border-gray-300 rounded-md bg-white hover:bg-gray-50 text-gray-700 font-medium text-sm transition-colors shadow-sm">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.014a4.514 4.514 0 011.513 1.036m-5.106-.233L4 3.339m8.583 6.551l-3.33-3.33m-1.228 3.518L6.87 8.92" /></svg>
                        Rebuild OS
                    </button>
                </form>
                
                <form method="POST" action="{{ route('dashboard.destroy', $vps) }}" class="flex-1" data-confirm="🗑️ Xóa VPS này? Hành động này KHÔNG THỂ hoàn tác.">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full flex justify-center items-center gap-2 px-4 py-2.5 border border-transparent rounded-md bg-red-600 hover:bg-red-700 text-white font-medium text-sm transition-colors shadow-sm">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                        Xóa VPS
                    </button>
                </form>
            </div>
        </div>
        
    </div>
    
    {{-- Right Sidebar Column --}}
    <div class="space-y-6">
        
        {{-- Power Actions Card --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-sm font-semibold text-gray-900">Quản lý nguồn</h3>
            </div>
            
            <div class="p-4 space-y-3">
                <form method="POST" action="{{ route('dashboard.boot', $vps) }}" data-confirm="Bật nguồn VPS?">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 border border-gray-200 rounded-lg hover:border-cloud-300 hover:bg-cloud-50 transition-colors text-left group">
                        <svg class="text-gray-400 group-hover:text-cloud-600" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1012.728 0M12 3v9" /></svg>
                        <span class="text-sm font-medium text-gray-700 group-hover:text-cloud-700">Boot</span>
                    </button>
                </form>
                
                <form method="POST" action="{{ route('dashboard.reboot', $vps) }}" data-confirm="Khởi động lại VPS?">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 border border-gray-200 rounded-lg hover:border-cloud-300 hover:bg-cloud-50 transition-colors text-left group">
                        <svg class="text-gray-400 group-hover:text-cloud-600" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                        <span class="text-sm font-medium text-gray-700 group-hover:text-cloud-700">Reboot</span>
                    </button>
                </form>
                
                <div class="pt-2">
                    <form method="POST" action="{{ route('dashboard.shutdown', $vps) }}" data-confirm="Tắt nguồn VPS?">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 border border-red-100 bg-red-50/50 rounded-lg hover:border-red-300 hover:bg-red-50 transition-colors text-left group">
                            <svg class="text-red-500" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M9 9.563C9 9.252 9.252 9 9.563 9h4.874c.311 0 .563.252.563.563v4.874c0 .311-.252.563-.563.563H9.564A.562.562 0 019 14.437V9.564z" /></svg>
                            <span class="text-sm font-medium text-red-700">Force Shutdown</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        {{-- Change Password Card --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-sm font-semibold text-gray-900">Đổi mật khẩu</h3>
            </div>
            <div class="p-4">
                <p class="text-xs text-gray-500 mb-4">VPS sẽ bị khởi động lại để áp dụng mật khẩu mới.</p>
                <form method="POST" action="{{ route('dashboard.password', $vps) }}" data-confirm="Đổi mật khẩu root? VPS sẽ bị tắt trong ~60s.">
                    @csrf
                    <input type="password" name="new_password" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm mb-3 focus:border-cloud-600 focus:ring-1 focus:ring-cloud-600" placeholder="Mật khẩu mới (≥11 ký tự)" minlength="11" required>
                    <button type="submit" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-md transition-colors text-sm">
                        Cập nhật mật khẩu
                    </button>
                </form>
            </div>
        </div>
        
    </div>
</div>

@endsection

@push('scripts')
<script>


// Copy to clipboard
function copyText(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = '<svg width="16" height="16" fill="none" stroke="#10b981" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>';
        setTimeout(() => { btn.innerHTML = orig; }, 2000);
    });
}

// Auto-polling trạng thái
(function () {
    const PENDING = ['Đang khởi tạo...','Đang khởi động','Đang tắt','Đang khởi động lại','Đang rebuild...','Đang migration...'];
    const URL     = @json(route('dashboard.status', $vps));
    let status    = @json($vps->status);
    let timer     = null;
    let count     = 0;

    function isPending(s) { return PENDING.some(p => s.startsWith(p)); }

    function updateUI(data) {
        status = data.status;
        const badge = document.getElementById('vps-badge');
        
        // Update badge DOM
        badge.innerHTML = `<span class="w-1.5 h-1.5 rounded-full ${data.ready ? 'bg-green-500' : (data.status.includes('Lỗi') ? 'bg-red-500' : 'bg-yellow-500')}"></span> ${data.status}`;
        badge.className = `inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold ${data.ready ? 'bg-green-50 text-green-700 border border-green-200' : (data.status.includes('Lỗi') ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-yellow-50 text-yellow-700 border border-yellow-200')}`;

        if (data.public_ip) {
            const ip = document.getElementById('vps-ip');
            if (ip) ip.textContent = data.public_ip;
            const ssh = document.getElementById('ssh-cmd');
            const isWindows = @json($isWindows);
            if (ssh) ssh.textContent = isWindows ? 'Administrator' : 'ssh root@' + data.public_ip;
        }
    }

    function stopPoll() {
        clearInterval(timer); timer = null;
        document.getElementById('polling-indicator').style.display = 'none';
    }

    function poll() {
        if (++count > 72) { stopPoll(); return; }
        fetch(URL, { headers: { Accept: 'application/json' } })
            .then(r => r.json())
            .then(data => {
                updateUI(data);
                if (!isPending(data.status)) stopPoll();
            })
            .catch(() => {});
    }

    if (isPending(status)) {
        const ind = document.getElementById('polling-indicator');
        ind.style.display = 'inline-flex';
        poll();
        timer = setInterval(poll, 5000);
    }
})();
</script>
@endpush
