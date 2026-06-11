@extends('layouts.app')
@section('title', 'Thông tin cá nhân — SeaServer')

@section('breadcrumbs')
    <span>Tài khoản</span>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-900">Hồ sơ cá nhân</span>
@endsection

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Hồ sơ cá nhân</h1>
    <p class="text-sm text-gray-500 mt-1">Quản lý thông tin tài khoản, bảo mật và xem thống kê sử dụng.</p>
</div>

{{-- Stats Row --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    {{-- Balance --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 flex items-center">
        <div class="w-12 h-12 rounded-full bg-cloud-50 flex items-center justify-center text-cloud-600 mr-4">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Số dư hiện tại</p>
            <p class="text-2xl font-extrabold text-gray-900">{{ number_format($user->balance) }} đ</p>
        </div>
    </div>

    {{-- Total Deposited --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 flex items-center">
        <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center text-green-600 mr-4">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Tổng tiền đã nạp</p>
            <p class="text-2xl font-extrabold text-gray-900">{{ number_format($totalDeposited) }} đ</p>
        </div>
    </div>

    {{-- Active Services --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 flex items-center">
        <div class="w-12 h-12 rounded-full bg-purple-50 flex items-center justify-center text-purple-600 mr-4">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-16.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a4.5 4.5 0 01.9-2.7L5.737 5.1a3.375 3.375 0 012.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 01.9 2.7m0 0a3 3 0 01-3 3m0 3h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008zm-3 6h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008z" /></svg>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Dịch vụ đang hoạt động</p>
            <p class="text-2xl font-extrabold text-gray-900">{{ $activeVpsCount }} VPS</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    {{-- Left Col: Profile Info --}}
    <div class="space-y-8">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-900">Thông tin cá nhân</h2>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">Đã xác minh</span>
            </div>
            <div class="p-6">
                <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-br from-cloud-500 to-cloud-700 text-white rounded-full flex items-center justify-center font-bold text-2xl uppercase shadow-md">
                        {{ substr($user->email, 0, 2) }}
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $user->name }}</h3>
                        <p class="text-gray-500 text-sm">ID Thành viên: #{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Họ và tên</span>
                        <div class="px-4 py-2 bg-gray-50 rounded-lg text-sm text-gray-900 font-medium border border-gray-100">{{ $user->name }}</div>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Địa chỉ Email</span>
                        <div class="px-4 py-2 bg-gray-50 rounded-lg text-sm font-mono text-gray-900 border border-gray-100">{{ $user->email }}</div>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Ngày tham gia</span>
                        <div class="px-4 py-2 bg-gray-50 rounded-lg text-sm text-gray-900 font-medium border border-gray-100">{{ $user->created_at->format('d/m/Y H:i') }} ({{ $user->created_at->diffForHumans() }})</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Col: Change Password --}}
    <div class="space-y-8">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50">
                <h2 class="text-lg font-bold text-gray-900">Bảo mật & Đổi mật khẩu</h2>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('profile.password') }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">Mật khẩu hiện tại</label>
                        <input type="password" name="current_password" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-cloud-600 focus:ring-1 focus:ring-cloud-600 transition-shadow @error('current_password') border-red-500 @enderror"
                               placeholder="Nhập mật khẩu hiện tại...">
                        @error('current_password')<p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div class="pt-2">
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">Mật khẩu mới</label>
                        <input type="password" name="password" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-cloud-600 focus:ring-1 focus:ring-cloud-600 transition-shadow @error('password') border-red-500 @enderror"
                               placeholder="Tối thiểu 8 ký tự...">
                        @error('password')<p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">Xác nhận mật khẩu mới</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-cloud-600 focus:ring-1 focus:ring-cloud-600 transition-shadow"
                               placeholder="Nhập lại mật khẩu mới...">
                    </div>

                    <div class="pt-4 border-t border-gray-100">
                        <button type="submit" class="w-full flex justify-center items-center gap-2 py-3 px-4 rounded-lg shadow-sm text-sm font-bold text-white bg-cloud-600 hover:bg-cloud-700 transition-colors">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                            Cập nhật Mật khẩu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
