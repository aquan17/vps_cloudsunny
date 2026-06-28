@extends('layouts.app')
@section('title', 'Thông báo — SeaServer')

@section('breadcrumbs')
    <span>Thông báo hệ thống</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Thông báo</h1>
    </div>

    <div class="bg-gradient-to-br from-indigo-50 to-white rounded-xl shadow-sm border border-indigo-100 overflow-hidden">
        <div class="bg-indigo-600 px-6 py-4 flex items-center gap-3">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" class="text-white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg>
            <h3 class="text-white font-bold text-lg">🔥 CHÍNH THỨC MỞ TÍNH NĂNG AFFILIATE & TÌM ĐỐI TÁC API ĐẠI LÝ 🔥</h3>
        </div>
        <div class="p-6 text-gray-700 space-y-6 leading-relaxed">
            <p>Chào anh em, để hỗ trợ anh em kiếm thêm thu nhập thụ động bền vững cũng như mở rộng kinh doanh, hệ thống SeaServer vừa cập nhật 2 chương trình cực mượt:</p>
            
            <div class="space-y-3">
                <p class="font-bold text-gray-900 text-lg">1️⃣ CHƯƠNG TRÌNH AFFILIATE - HOA HỒNG 5% TRỌN ĐỜI 💸</p>
                <p>Chỉ cần ae copy Link giới thiệu gửi cho bạn bè/khách hàng đăng ký tài khoản. Khách mua mới VPS, gia hạn VPS, nâng cấp cấu hình hay mua Proxy... ae đều tự động được "ting ting" 5% hoa hồng vào tài khoản.</p>
                <p>Trọn đời! Khách cứ dùng dịch vụ và gia hạn tháng nào là ae có tiền tháng đó. Tiền cộng thẳng vào số dư tự động 100%.</p>
                <div class="bg-indigo-50/80 p-4 rounded-lg border border-indigo-100">
                    <p class="text-indigo-700 font-medium">👉 Ae nhìn sang thanh Menu bên trái chọn mục "Affiliate" để lấy link và mã ngay nhé!</p>
                </div>
            </div>

            <div class="space-y-3">
                <p class="font-bold text-gray-900 text-lg">2️⃣ TÌM ĐỐI TÁC ĐẤU NỐI API - BÁN LẠI VPS (RESELLER) 🤝</p>
                <p>Ae nào có sẵn tệp khách hàng, có web riêng hoặc muốn mở dịch vụ bán VPS kiếm lời? SeaServer chính thức cung cấp cổng API cho phép anh em đấu nối hệ thống để tự động hóa việc tạo, xóa, gia hạn VPS ngay trên web của anh em.</p>
                <p>Mình sẽ hỗ trợ tài liệu và hướng dẫn code từ A-Z để anh em tích hợp nhanh nhất. Giá rổ cam kết cực kỳ tối ưu để anh em bán lại có biên độ lợi nhuận ngon.</p>
            </div>

            <div class="p-4 bg-indigo-50 border border-indigo-200 rounded-xl font-medium text-indigo-900 shadow-sm">
                <p>💬 Ae nào muốn bào Affiliate thì cứ lấy link quất luôn. Còn ae nào muốn chơi lớn đấu API làm đại lý thì inbox trực tiếp mình để trao đổi chính sách và lấy tài liệu nhé!</p>
            </div>
            
            <div class="pt-4 border-t border-gray-100 text-center">
                <p class="font-bold text-lg text-indigo-600">Chúc ae một ngày bùng nổ doanh số! 🚀</p>
            </div>
        </div>
    </div>
</div>
@endsection
