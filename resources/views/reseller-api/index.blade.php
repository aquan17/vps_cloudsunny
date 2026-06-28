@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

    <!-- Page header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Reseller API Portal</h1>
            <p class="text-sm text-gray-500 mt-1">Quản lý xác thực và tài liệu kết nối API dành cho Đối tác</p>
        </div>
    </div>

    @if (session('success'))
    <div class="mb-4 rounded-md bg-green-50 p-4 border border-green-200">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Thông tin cấu hình (Grid 2 cột) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Credentials Box -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h2 class="text-base font-bold text-gray-900">1. Thông tin xác thực (Credentials)</h2>
            </div>
            <div class="p-6 space-y-5">
                <div>
                    <span class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">API Endpoint Gốc</span>
                    <div class="flex items-center space-x-2">
                        <input type="text" readonly value="https://seaserver.site/api/v1" class="flex-1 block w-full px-3 py-2 rounded-md border border-gray-300 bg-gray-50 text-gray-900 text-sm font-mono outline-none focus:ring-0" id="endpointInput">
                        <button type="button" onclick="copyToClipboard('endpointInput')" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Copy
                        </button>
                    </div>
                </div>

                <div>
                    <span class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">API Key (Bearer Token)</span>
                    <div class="flex items-center space-x-2 relative">
                        <input type="password" readonly value="{{ $apiKey }}" class="flex-1 block w-full pl-3 pr-10 py-2 rounded-md border border-gray-300 bg-gray-50 text-gray-900 text-sm font-mono outline-none" id="apiKeyInput">
                        <button type="button" onclick="togglePassword()" class="absolute right-[85px] text-gray-400 hover:text-gray-600 focus:outline-none">
                            <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                        <button type="button" onclick="copyToClipboard('apiKeyInput')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-cloud-600 hover:bg-cloud-700">
                            Copy
                        </button>
                    </div>
                    <p class="text-xs text-red-500 mt-2 font-medium">Truyền khóa này vào thuộc tính Authorization Headers ở mỗi Request.</p>
                </div>
            </div>
        </div>

        <!-- Webhook Box -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h2 class="text-base font-bold text-gray-900">2. Cấu hình Webhook Callback</h2>
            </div>
            <div class="p-6">
                <p class="text-sm text-gray-500 mb-4">Nhận thông báo tự động (POST JSON) từ hệ thống ngay lập tức khi VPS tạo hoàn tất. Khuyến nghị sử dụng để tránh quá tải API Kiểm tra trạng thái.</p>
                <form action="{{ route('reseller.api.webhook') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <span class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">URL Nhận thông báo (Của bạn)</span>
                        <input type="url" name="webhook_url" value="{{ $webhookUrl }}" placeholder="https://your-domain.com/api/webhook" class="block w-full px-4 py-2 rounded-md border border-gray-300 text-sm font-mono focus:border-cloud-500 focus:ring-cloud-500">
                        @error('webhook_url') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" class="w-full justify-center inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-800 hover:bg-gray-900">
                        Cập nhật Webhook
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- API Documentation (Tab Style) -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden flex flex-col xl:flex-row min-h-[600px]">
        <!-- Sidebar Tabs -->
        <div class="w-full xl:w-72 bg-gray-50 border-b xl:border-b-0 xl:border-r border-gray-200 p-4 shrink-0">
            <h2 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4 pl-2">Tài liệu API</h2>
            <nav class="space-y-1">
                <button onclick="switchTab('intro')" id="tab-intro" class="api-tab w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md bg-white text-cloud-700 shadow-sm border border-gray-200">
                    <span>Giới thiệu chung</span>
                </button>
                
                <button onclick="switchTab('get-profile')" id="tab-get-profile" class="api-tab w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-white hover:text-gray-900 hover:shadow-sm hover:border border border-transparent transition-all">
                    <span>Thông tin Tài khoản</span>
                    <span class="bg-blue-100 text-blue-700 text-[10px] px-1.5 py-0.5 rounded font-bold">GET</span>
                </button>

                <button onclick="switchTab('get-info')" id="tab-get-info" class="api-tab w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-white hover:text-gray-900 hover:shadow-sm hover:border border border-transparent transition-all">
                    <span>Danh mục & Bảng giá</span>
                    <span class="bg-blue-100 text-blue-700 text-[10px] px-1.5 py-0.5 rounded font-bold">GET</span>
                </button>

                <button onclick="switchTab('post-vps')" id="tab-post-vps" class="api-tab w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-white hover:text-gray-900 hover:shadow-sm hover:border border border-transparent transition-all">
                    <span>Tạo mới VPS</span>
                    <span class="bg-green-100 text-green-700 text-[10px] px-1.5 py-0.5 rounded font-bold">POST</span>
                </button>

                <button onclick="switchTab('get-status')" id="tab-get-status" class="api-tab w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-white hover:text-gray-900 hover:shadow-sm hover:border border border-transparent transition-all">
                    <span>Kiểm tra trạng thái</span>
                    <span class="bg-blue-100 text-blue-700 text-[10px] px-1.5 py-0.5 rounded font-bold">GET</span>
                </button>

                <button onclick="switchTab('webhook-info')" id="tab-webhook-info" class="api-tab w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-white hover:text-gray-900 hover:shadow-sm hover:border border border-transparent transition-all">
                    <span>Webhook Trả về</span>
                    <span class="bg-purple-100 text-purple-700 text-[10px] px-1.5 py-0.5 rounded font-bold">HOOK</span>
                </button>
                
                <button onclick="switchTab('errors')" id="tab-errors" class="api-tab w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-white hover:text-gray-900 hover:shadow-sm hover:border border border-transparent transition-all">
                    <span>Bảng Mã Lỗi</span>
                </button>
            </nav>
        </div>

        <!-- Content Area -->
        <div class="flex-1 p-6 md:p-8 overflow-y-auto bg-white max-h-[800px]">
            
            <!-- Tab: Intro -->
            <div id="content-intro" class="api-content">
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Hướng dẫn Tích hợp SeaServer API</h3>
                <p class="text-gray-600 mb-6">Tài liệu kỹ thuật cung cấp toàn bộ quy chuẩn gọi API. Hỗ trợ đầy đủ các thao tác Mua máy chủ (VPS), Kiểm tra trạng thái và Nhận Webhook.</p>
                
                <div class="bg-gray-50 rounded-lg p-5 border border-gray-200 mb-6">
                    <h4 class="font-bold text-gray-900 mb-2">Quy tắc Xác thực (Authentication)</h4>
                    <p class="text-sm text-gray-600 mb-4">Hệ thống áp dụng chuẩn OAuth 2.0 (Bearer Token). Truyền mã API Key của bạn vào header của từng request:</p>
                    <div class="bg-gray-900 rounded-md p-4 shadow-inner">
<pre class="text-gray-300 text-sm font-mono leading-relaxed"><code><span class="text-green-400">Authorization:</span> Bearer YOUR_API_KEY
<span class="text-green-400">Accept:</span> application/json
<span class="text-green-400">Content-Type:</span> application/json</code></pre>
                    </div>
                </div>
                
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200 flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-bold mb-1">Môi trường Production Base URL:</p>
                        <code class="bg-blue-100 px-2 py-1 rounded text-blue-900 font-mono">https://seaserver.site/api/v1</code>
                    </div>
                </div>
            </div>

            <!-- Tab: PROFILE INFO -->
            <div id="content-get-profile" class="api-content hidden">
                <div class="flex items-center gap-3 mb-4">
                    <span class="bg-blue-100 text-blue-700 text-sm px-2.5 py-1 rounded font-bold">GET</span>
                    <h3 class="text-2xl font-bold text-gray-900 break-all">https://seaserver.site/api/v1/profile</h3>
                </div>
                <p class="text-gray-600 mb-6">Truy xuất thông tin số dư tài khoản hiện tại của bạn để kiểm tra trước khi thực hiện mua VPS.</p>
                
                <h4 class="font-bold text-gray-900 mb-2 border-b pb-2 flex items-center justify-between">
                    <span>Response Trả về (JSON)</span>
                    <span class="bg-emerald-100 text-emerald-800 text-xs px-2 py-0.5 rounded-full">200 OK</span>
                </h4>
                <div class="bg-gray-900 rounded-md p-4 shadow-inner mb-6">
<pre class="text-gray-300 text-sm font-mono overflow-x-auto leading-relaxed"><code>{
  <span class="text-blue-400">"status"</span>: <span class="text-yellow-300">"success"</span>,
  <span class="text-blue-400">"data"</span>: {
    <span class="text-blue-400">"email"</span>: <span class="text-yellow-300">"reseller@example.com"</span>,
    <span class="text-blue-400">"balance"</span>: <span class="text-orange-400">5500000</span>, <span class="text-gray-500">// Số dư hiện tại (VND)</span>
    <span class="text-blue-400">"vps_active"</span>: <span class="text-orange-400">12</span>,   <span class="text-gray-500">// Số lượng VPS đang chạy</span>
    <span class="text-blue-400">"created_at"</span>: <span class="text-yellow-300">"2026-01-15T08:30:00Z"</span>
  }
}</code></pre>
                </div>
            </div>

            <!-- Tab: GET INFO -->
            <div id="content-get-info" class="api-content hidden">
                <div class="flex items-center gap-3 mb-4">
                    <span class="bg-blue-100 text-blue-700 text-sm px-2.5 py-1 rounded font-bold">GET</span>
                    <h3 class="text-2xl font-bold text-gray-900 break-all">https://seaserver.site/api/v1/plans</h3>
                </div>
                <p class="text-gray-600 mb-6">Truy xuất danh sách Gói Cấu hình (Plans) và Hệ Điều Hành (OS) tại Server Việt Nam để hiển thị lên Website của bạn.</p>
                
                <h4 class="font-bold text-gray-900 mb-2 border-b pb-2 flex items-center justify-between">
                    <span>Response Trả về (JSON)</span>
                    <span class="bg-emerald-100 text-emerald-800 text-xs px-2 py-0.5 rounded-full">200 OK</span>
                </h4>
                <div class="bg-gray-900 rounded-md p-4 shadow-inner mb-6">
<pre class="text-gray-300 text-sm font-mono overflow-x-auto leading-relaxed"><code>{
  <span class="text-blue-400">"status"</span>: <span class="text-yellow-300">"success"</span>,
  <span class="text-blue-400">"data"</span>: {
    <span class="text-blue-400">"location"</span>: <span class="text-yellow-300">"Vietnam (VN)"</span>,
    <span class="text-blue-400">"os_list"</span>: [
      { <span class="text-blue-400">"id"</span>: <span class="text-orange-400">10</span>, <span class="text-blue-400">"name"</span>: <span class="text-yellow-300">"Ubuntu 22.04 LTS"</span>, <span class="text-blue-400">"type"</span>: <span class="text-yellow-300">"linux"</span> },
      { <span class="text-blue-400">"id"</span>: <span class="text-orange-400">20</span>, <span class="text-blue-400">"name"</span>: <span class="text-yellow-300">"Windows Server 2022"</span>, <span class="text-blue-400">"type"</span>: <span class="text-yellow-300">"windows"</span> }
    ],
    <span class="text-blue-400">"plans"</span>: [
      {
        <span class="text-blue-400">"id"</span>: <span class="text-orange-400">5</span>,
        <span class="text-blue-400">"name"</span>: <span class="text-yellow-300">"Basic 1 Core - 1GB RAM"</span>,
        <span class="text-blue-400">"cpu"</span>: <span class="text-orange-400">1</span>,
        <span class="text-blue-400">"ram"</span>: <span class="text-orange-400">1024</span>,
        <span class="text-blue-400">"disk"</span>: <span class="text-orange-400">25</span>,
        <span class="text-blue-400">"price_monthly"</span>: <span class="text-orange-400">85000</span> <span class="text-gray-500">// Giá VND Đại lý phải trả</span>
      }
    ]
  }
}</code></pre>
                </div>
            </div>

            <!-- Tab: POST VPS -->
            <div id="content-post-vps" class="api-content hidden">
                <div class="flex items-center gap-3 mb-4">
                    <span class="bg-green-100 text-green-700 text-sm px-2.5 py-1 rounded font-bold">POST</span>
                    <h3 class="text-2xl font-bold text-gray-900 break-all">https://seaserver.site/api/v1/vps/create</h3>
                </div>
                <p class="text-gray-600 mb-6">Thực hiện mua và khởi tạo VPS Server Việt Nam. Tiền sẽ được trừ vào số dư tài khoản của bạn. API hoạt động theo cơ chế <strong class="text-gray-900">Bất đồng bộ (Async)</strong>.</p>

                <h4 class="font-bold text-gray-900 mb-2">Body Request (JSON Raw)</h4>
                <div class="bg-gray-900 rounded-md p-4 shadow-inner mb-6">
<pre class="text-gray-300 text-sm font-mono overflow-x-auto leading-relaxed"><code>{
  <span class="text-blue-400">"plan_id"</span>: <span class="text-orange-400">5</span>,          <span class="text-gray-500">// ID của gói cấu hình lấy từ danh mục</span>
  <span class="text-blue-400">"os_id"</span>: <span class="text-orange-400">10</span>,           <span class="text-gray-500">// ID Hệ điều hành lấy từ danh mục</span>
  <span class="text-blue-400">"duration"</span>: <span class="text-orange-400">1</span>,         <span class="text-gray-500">// Số tháng gia hạn (1, 3, 6, 12)</span>
  <span class="text-blue-400">"hostname"</span>: <span class="text-yellow-300">"server-1"</span> <span class="text-gray-500">// (Tùy chọn) Tên gợi nhớ máy chủ</span>
}</code></pre>
                </div>

                <h4 class="font-bold text-gray-900 mb-2 border-b pb-2 flex items-center justify-between">
                    <span>Response Trả về (JSON)</span>
                    <span class="bg-amber-100 text-amber-800 text-xs px-2 py-0.5 rounded-full">202 Accepted</span>
                </h4>
                <p class="text-sm text-gray-600 mb-3">Mã 202 báo hiệu yêu cầu đã được đưa vào hàng đợi xử lý. Thời gian tạo thực tế từ 1-3 phút.</p>
                <div class="bg-gray-900 rounded-md p-4 shadow-inner mb-2">
<pre class="text-gray-300 text-sm font-mono overflow-x-auto leading-relaxed"><code>{
  <span class="text-blue-400">"status"</span>: <span class="text-green-400">202</span>,
  <span class="text-blue-400">"message"</span>: <span class="text-yellow-300">"VPS đang được khởi tạo. Hệ thống sẽ tự động gọi Webhook khi hoàn tất."</span>,
  <span class="text-blue-400">"data"</span>: {
    <span class="text-blue-400">"order_id"</span>: <span class="text-yellow-300">"ORD-87123"</span>,
    <span class="text-blue-400">"amount_deducted"</span>: <span class="text-orange-400">85000</span>,
    <span class="text-blue-400">"balance_left"</span>: <span class="text-orange-400">1500000</span>,
    <span class="text-blue-400">"status"</span>: <span class="text-yellow-300">"processing"</span>,
    <span class="text-blue-400">"status_url"</span>: <span class="text-yellow-300">"https://seaserver.site/api/v1/vps/ORD-87123/status"</span>
  }
}</code></pre>
                </div>
            </div>
            
            <!-- Tab: GET STATUS -->
            <div id="content-get-status" class="api-content hidden">
                <div class="flex items-center gap-3 mb-4">
                    <span class="bg-blue-100 text-blue-700 text-sm px-2.5 py-1 rounded font-bold">GET</span>
                    <h3 class="text-2xl font-bold text-gray-900 break-all">https://seaserver.site/api/v1/vps/{order_id}/status</h3>
                </div>
                <p class="text-gray-600 mb-4">Dùng trong trường hợp hệ thống của bạn <strong class="text-gray-900">Không hỗ trợ Webhook</strong>. Bạn có thể gọi thủ công API này sau mỗi 15-30 giây để lấy thông tin VPS khi trạng thái chuyển sang <code>active</code>.</p>
                
                <h4 class="font-bold text-gray-900 mb-2 border-b pb-2 flex items-center justify-between">
                    <span>Response Trả về (JSON)</span>
                    <span class="bg-emerald-100 text-emerald-800 text-xs px-2 py-0.5 rounded-full">200 OK</span>
                </h4>
                <div class="bg-gray-900 rounded-md p-4 shadow-inner">
<pre class="text-gray-300 text-sm font-mono overflow-x-auto leading-relaxed"><code>{
  <span class="text-blue-400">"status"</span>: <span class="text-yellow-300">"success"</span>,
  <span class="text-blue-400">"data"</span>: {
    <span class="text-blue-400">"order_id"</span>: <span class="text-yellow-300">"ORD-87123"</span>,
    <span class="text-blue-400">"status"</span>: <span class="text-yellow-300">"active"</span>, <span class="text-gray-500">// "processing" | "active" | "failed"</span>
    <span class="text-blue-400">"vps_info"</span>: {
       <span class="text-blue-400">"ip_address"</span>: <span class="text-yellow-300">"103.22.33.44"</span>,
       <span class="text-blue-400">"username"</span>: <span class="text-yellow-300">"root"</span>,
       <span class="text-blue-400">"password"</span>: <span class="text-yellow-300">"Pa$$w0rd123"</span>,
       <span class="text-blue-400">"os_name"</span>: <span class="text-yellow-300">"Ubuntu 22.04 LTS"</span>,
       <span class="text-blue-400">"created_at"</span>: <span class="text-yellow-300">"2026-06-28 14:00:00"</span>,
       <span class="text-blue-400">"expired_at"</span>: <span class="text-yellow-300">"2026-07-28 14:00:00"</span>
    }
  }
}</code></pre>
                </div>
            </div>

            <!-- Tab: Webhook -->
            <div id="content-webhook-info" class="api-content hidden">
                <div class="flex items-center gap-3 mb-4">
                    <span class="bg-purple-100 text-purple-700 text-sm px-2.5 py-1 rounded font-bold">HOOK</span>
                    <h3 class="text-2xl font-bold text-gray-900">Sự kiện: vps.created</h3>
                </div>
                <p class="text-gray-600 mb-6">Mô phỏng dữ liệu JSON mà SeaServer sẽ đẩy ngược về <code>URL Webhook</code> của bạn khi VPS khởi tạo thành công 100%.</p>
                
                <h4 class="font-bold text-gray-900 mb-2">Payload (SeaServer -> Bạn)</h4>
                <div class="bg-gray-900 rounded-md p-4 shadow-inner mb-4">
<pre class="text-gray-300 text-sm font-mono overflow-x-auto leading-relaxed"><code>{
  <span class="text-blue-400">"event"</span>: <span class="text-yellow-300">"vps.created"</span>,
  <span class="text-blue-400">"order_id"</span>: <span class="text-yellow-300">"ORD-87123"</span>,
  <span class="text-blue-400">"timestamp"</span>: <span class="text-orange-400">1719560400</span>,
  <span class="text-blue-400">"data"</span>: {
    <span class="text-blue-400">"vps_id"</span>: <span class="text-orange-400">99</span>,
    <span class="text-blue-400">"ip_address"</span>: <span class="text-yellow-300">"103.22.33.44"</span>,
    <span class="text-blue-400">"ssh_port"</span>: <span class="text-orange-400">22</span>,
    <span class="text-blue-400">"username"</span>: <span class="text-yellow-300">"root"</span>,
    <span class="text-blue-400">"password"</span>: <span class="text-yellow-300">"SeaServer!@#88"</span>,
    <span class="text-blue-400">"plan"</span>: <span class="text-yellow-300">"1 Core / 1GB RAM / 25GB NVMe"</span>,
    <span class="text-blue-400">"expired_at"</span>: <span class="text-yellow-300">"2026-07-28 14:00:00"</span>
  }
}</code></pre>
                </div>
                <div class="bg-amber-50 border border-amber-200 rounded p-4 text-sm text-amber-800">
                    <strong class="font-bold block mb-1">Yêu cầu xác nhận (Acknowledge)</strong>
                    API (URL Webhook) của bạn bắt buộc phải trả về HTTP Code <code>200 OK</code> để báo hiệu bạn đã nhận được tin. Nếu báo lỗi (500) hoặc không phản hồi (Timeout), SeaServer sẽ tiếp tục gửi lại liên tục mỗi phút (tối đa 3 lần).
                </div>
            </div>

            <!-- Tab: Errors -->
            <div id="content-errors" class="api-content hidden">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Tổng hợp Mã lỗi (HTTP Status Codes)</h3>
                
                <div class="space-y-4">
                    <!-- Error 401 -->
                    <div class="flex items-start border border-gray-200 rounded-lg p-4 bg-gray-50 hover:bg-white transition-colors">
                        <div class="bg-amber-100 text-amber-800 text-sm font-bold px-3 py-1.5 rounded shrink-0 w-16 text-center">401</div>
                        <div class="ml-4">
                            <h5 class="font-bold text-gray-900">Unauthorized</h5>
                            <p class="text-sm text-gray-600 mt-1">Lỗi do bạn quên truyền API Key trong Header, hoặc API Key bị sai/đã bị thu hồi.</p>
                        </div>
                    </div>

                    <!-- Error 400 -->
                    <div class="flex items-start border border-gray-200 rounded-lg p-4 bg-gray-50 hover:bg-white transition-colors">
                        <div class="bg-amber-100 text-amber-800 text-sm font-bold px-3 py-1.5 rounded shrink-0 w-16 text-center">400</div>
                        <div class="ml-4">
                            <h5 class="font-bold text-gray-900">Bad Request (Validation Error)</h5>
                            <p class="text-sm text-gray-600 mt-1">Thiếu tham số bắt buộc hoặc sai định dạng. <br>Hệ thống sẽ trả về lỗi chi tiết. Ví dụ: <code class="bg-gray-100 border border-gray-200 px-1 rounded text-red-600 font-mono text-xs">{"message": "plan_id is required"}</code></p>
                        </div>
                    </div>

                    <!-- Error 402 -->
                    <div class="flex items-start border border-gray-200 rounded-lg p-4 bg-gray-50 hover:bg-white transition-colors">
                        <div class="bg-amber-100 text-amber-800 text-sm font-bold px-3 py-1.5 rounded shrink-0 w-16 text-center">402</div>
                        <div class="ml-4">
                            <h5 class="font-bold text-gray-900">Payment Required</h5>
                            <p class="text-sm text-gray-600 mt-1">Số dư Đại lý của bạn không đủ để thanh toán cho cấu hình vừa chọn. Vui lòng nạp thêm tiền.</p>
                        </div>
                    </div>
                    
                    <!-- Error 404 -->
                    <div class="flex items-start border border-gray-200 rounded-lg p-4 bg-gray-50 hover:bg-white transition-colors">
                        <div class="bg-amber-100 text-amber-800 text-sm font-bold px-3 py-1.5 rounded shrink-0 w-16 text-center">404</div>
                        <div class="ml-4">
                            <h5 class="font-bold text-gray-900">Not Found</h5>
                            <p class="text-sm text-gray-600 mt-1">Gói VPS, Hệ điều hành hoặc Order ID không tồn tại trong hệ thống.</p>
                        </div>
                    </div>

                    <!-- Error 500 -->
                    <div class="flex items-start border border-red-100 rounded-lg p-4 bg-red-50">
                        <div class="bg-red-100 text-red-800 text-sm font-bold px-3 py-1.5 rounded shrink-0 w-16 text-center">500</div>
                        <div class="ml-4">
                            <h5 class="font-bold text-red-900">Internal Server Error</h5>
                            <p class="text-sm text-red-700 mt-1">Lỗi xuất phát từ máy chủ SeaServer. VPS chưa bị trừ tiền. Vui lòng liên hệ bộ phận Kỹ thuật hỗ trợ hoặc thử lại sau vài phút.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<script>
    function copyToClipboard(elementId) {
        var copyText = document.getElementById(elementId);
        var isPassword = (copyText.type === 'password');
        if (isPassword) copyText.type = 'text';
        
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);
        
        if (isPassword) copyText.type = 'password';
        alert("Đã copy dữ liệu vào clipboard!");
    }

    function togglePassword() {
        var input = document.getElementById("apiKeyInput");
        var icon = document.getElementById("eyeIcon");
        if (input.type === "password") {
            input.type = "text";
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />';
        } else {
            input.type = "password";
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
        }
    }

    function switchTab(tabId) {
        document.querySelectorAll('.api-content').forEach(function(el) {
            el.classList.add('hidden');
        });
        
        document.querySelectorAll('.api-tab').forEach(function(el) {
            el.classList.remove('bg-white', 'text-cloud-700', 'shadow-sm', 'border-gray-200');
            el.classList.add('text-gray-600', 'border-transparent');
        });

        document.getElementById('content-' + tabId).classList.remove('hidden');

        var activeBtn = document.getElementById('tab-' + tabId);
        activeBtn.classList.remove('text-gray-600', 'border-transparent');
        activeBtn.classList.add('bg-white', 'text-cloud-700', 'shadow-sm', 'border-gray-200');
    }
</script>
@endsection
