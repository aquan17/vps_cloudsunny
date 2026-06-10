@extends('layouts.guest')
@section('title', 'Đăng nhập — NovaCloud')

@section('content')
<div class="p-8">
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Đăng nhập</h1>
        <p class="text-gray-500 mt-2 text-sm">Truy cập dashboard quản lý hạ tầng Cloud</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf
        
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus 
                class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-cloud-500/20 focus:border-cloud-600 transition-colors"
                placeholder="nhapemail@congty.com">
            @error('email')
                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>
        
        <div>
            <div class="flex justify-between items-center mb-1.5">
                <label for="password" class="block text-sm font-medium text-gray-700">Mật khẩu</label>
            </div>
            <input type="password" id="password" name="password" required 
                class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-cloud-500/20 focus:border-cloud-600 transition-colors"
                placeholder="••••••••">
        </div>
        
        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-cloud-600 focus:ring-cloud-500"> 
                <span class="text-sm text-gray-600 select-none">Ghi nhớ đăng nhập</span>
            </label>
        </div>
        
        <button type="submit" class="w-full flex justify-center items-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-cloud-600 hover:bg-cloud-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cloud-500 transition-all">
            Đăng nhập
        </button>
    </form>
</div>

<div class="bg-gray-50 border-t border-gray-100 p-6 text-center">
    <p class="text-sm text-gray-600">
        Chưa có tài khoản? 
        <a href="{{ route('register') }}" class="font-semibold text-cloud-600 hover:text-cloud-700 hover:underline">Đăng ký ngay</a>
    </p>
</div>
@endsection
