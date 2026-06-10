@extends('layouts.guest')
@section('title', 'Đăng ký — NovaCloud')

@section('content')
<div class="p-8">
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Tạo tài khoản</h1>
        <p class="text-gray-500 mt-2 text-sm">Miễn phí — bắt đầu thuê VPS NovaCloud siêu tốc.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        
        <div>
            <div class="bg-blue-50 text-blue-800 p-3 rounded-lg text-sm mb-5 border border-blue-100 flex items-start gap-2">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span><strong>Lưu ý quan trọng:</strong> Vui lòng nhập chính xác email đang hoạt động của bạn. Hệ thống sẽ gửi một liên kết bắt buộc để kích hoạt tài khoản.</span>
            </div>
            
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Họ tên</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus 
                class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-cloud-500/20 focus:border-cloud-600 transition-colors"
                placeholder="Nguyễn Văn A">
            @error('name')
                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required 
                class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-cloud-500/20 focus:border-cloud-600 transition-colors"
                placeholder="nhapemail@congty.com">
            @error('email')
                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>
        
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Mật khẩu</label>
            <input type="password" id="password" name="password" required 
                class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-cloud-500/20 focus:border-cloud-600 transition-colors"
                placeholder="••••••••">
            @error('password')
                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>
        
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Xác nhận mật khẩu</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required 
                class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-cloud-500/20 focus:border-cloud-600 transition-colors"
                placeholder="••••••••">
        </div>
        
        <div class="pt-2">
            <button type="submit" class="w-full flex justify-center items-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-cloud-600 hover:bg-cloud-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cloud-500 transition-all">
                Tạo tài khoản
            </button>
        </div>
    </form>
</div>

<div class="bg-gray-50 border-t border-gray-100 p-6 text-center">
    <p class="text-sm text-gray-600">
        Đã có tài khoản? 
        <a href="{{ route('login') }}" class="font-semibold text-cloud-600 hover:text-cloud-700 hover:underline">Đăng nhập</a>
    </p>
</div>
@endsection
