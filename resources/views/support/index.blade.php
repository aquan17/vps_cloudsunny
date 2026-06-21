@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Trung tâm Hỗ trợ</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Zalo Support -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col items-center text-center hover:shadow-md transition-shadow">
            <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mb-4 text-[#0068FF]">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M21.544 11.045c.395-4.278-2.844-7.949-6.997-8.232-4.153-.284-7.857 2.92-8.252 7.197-.107 1.164.089 2.302.551 3.33-1.045 2.15-2.073 4.309-3.085 6.471.492-.128 1.011-.225 1.554-.265 2.164-.158 4.316.713 6.002 2.215.938.455 1.968.749 3.03.856 4.153.284 7.857-2.92 8.252-7.197.054-.585.011-1.166-.105-1.734-.029-.214.07-.433.256-.563.155-.108.286-.255.385-.429.288-.507.41-1.092.409-1.649zm-8.875 4.908c-1.572.352-3.14-.383-3.896-1.85-.353-.685-.432-1.458-.231-2.203.22-.816.78-1.488 1.547-1.849 1.571-.739 3.424-.075 4.163 1.496.353.684.432 1.457.23 2.202-.22.816-.78 1.488-1.547 1.849-.089.042-.18.08-.266.155zM15.42 8.441c-.413.568-1.121.84-1.802.731-.68-.11-1.258-.593-1.456-1.246-.197-.654.025-1.373.578-1.745.553-.372 1.303-.339 1.83.082.526.421.737 1.135.539 1.777-.04.137-.099.27-.189.395V8.44z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Hỗ trợ qua Zalo</h3>
            <p class="text-sm text-gray-500 mb-4 flex-grow">Nhắn tin trực tiếp với đội ngũ hỗ trợ qua Zalo cá nhân. Phản hồi nhanh chóng.</p>
            <a href="https://zalo.me/0862579104" target="_blank" rel="noopener noreferrer" class="w-full inline-flex justify-center items-center gap-2 px-4 py-2 bg-[#0068FF] text-white rounded-lg hover:bg-blue-600 font-medium transition-colors">
                Nhắn tin Zalo
            </a>
        </div>

        <!-- Phone Support -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col items-center text-center hover:shadow-md transition-shadow">
            <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mb-4 text-green-600">
                <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Hotline Khẩn cấp</h3>
            <p class="text-sm text-gray-500 mb-4 flex-grow">Cần xử lý gấp các vấn đề nghiêm trọng? Vui lòng gọi trực tiếp vào Hotline.</p>
            <a href="tel:0862579104" class="w-full inline-flex justify-center items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition-colors">
                Gọi 0862.579.104
            </a>
        </div>

        <!-- Facebook Support -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col items-center text-center hover:shadow-md transition-shadow">
            <div class="w-16 h-16 bg-indigo-50 rounded-full flex items-center justify-center mb-4 text-[#1877F2]">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Facebook Cá nhân</h3>
            <p class="text-sm text-gray-500 mb-4 flex-grow">Kết nối qua mạng xã hội Facebook để được hỗ trợ và cập nhật thông tin mới nhất.</p>
            <a href="https://www.facebook.com/anh.quaan.193935" target="_blank" rel="noopener noreferrer" class="w-full inline-flex justify-center items-center gap-2 px-4 py-2 bg-[#1877F2] text-white rounded-lg hover:bg-indigo-600 font-medium transition-colors">
                Liên hệ Facebook
            </a>
        </div>
    </div>

    <!-- Policies & FAQ -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
        <!-- Chính sách -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-orange-50 rounded-lg flex items-center justify-center text-orange-600">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                </div>
                <h2 class="text-lg font-bold text-gray-900">Chính sách Bảo hành</h2>
            </div>
            
            <ul class="space-y-4">
                <li class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <div>
                        <strong class="text-gray-900 block text-sm">Hoàn tiền 100% tự động</strong>
                        <span class="text-gray-600 text-sm">Hệ thống sẽ tự động hoàn tiền vào tài khoản nếu quá trình khởi tạo VPS/Proxy gặp lỗi từ phía máy chủ.</span>
                    </div>
                </li>
                <li class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    <div>
                        <strong class="text-gray-900 block text-sm">Trường hợp KHÔNG bảo hành</strong>
                        <span class="text-gray-600 text-sm">Không hoàn tiền cho VPS/Proxy đang hoạt động bình thường, hoặc bị khóa do khách hàng vi phạm quy định (DDoS, Scan SSH, vi phạm pháp luật...).</span>
                    </div>
                </li>
                <li class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <strong class="text-gray-900 block text-sm">Thời gian xử lý khiếu nại</strong>
                        <span class="text-gray-600 text-sm">Chúng tôi cam kết tiếp nhận và xử lý các sự cố kỹ thuật trong vòng 15-30 phút (trong giờ hành chính).</span>
                    </div>
                </li>
            </ul>
        </div>

        <!-- FAQ -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center text-purple-600">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <h2 class="text-lg font-bold text-gray-900">Câu hỏi thường gặp</h2>
            </div>

            <div class="space-y-4">
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                    <h4 class="font-semibold text-gray-900 text-sm mb-1">Nạp tiền qua QR Code mất bao lâu?</h4>
                    <p class="text-sm text-gray-600">Hệ thống của chúng tôi áp dụng công nghệ khớp lệnh ngân hàng tự động. Thường chỉ mất <strong>1 - 3 phút</strong> sau khi chuyển khoản là tiền sẽ tự động cộng vào tài khoản của bạn.</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                    <h4 class="font-semibold text-gray-900 text-sm mb-1">Mua VPS/Proxy xong bao lâu có IP?</h4>
                    <p class="text-sm text-gray-600">Đối với Proxy, thông tin thường được trả về <strong>ngay lập tức</strong>. Đối với VPS, hệ thống cần khoảng <strong>2 - 5 phút</strong> để tự động cài đặt hệ điều hành và thiết lập IP.</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                    <h4 class="font-semibold text-gray-900 text-sm mb-1">Tôi có thể tự cài lại Hệ điều hành (Reinstall) không?</h4>
                    <p class="text-sm text-gray-600">Hoàn toàn được! Bạn chỉ cần vào phần <strong>Quản lý VPS</strong>, chọn tính năng "Cài lại OS" và đợi hệ thống tự động chạy trong vài phút.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
