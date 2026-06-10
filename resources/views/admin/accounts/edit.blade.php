@extends('layouts.app')
@section('title', 'Sửa NovaCloud Account - Admin')

@section('breadcrumbs')
    <span>Quản trị</span>
    <span class="mx-2 text-gray-300">/</span>
    <a href="{{ route('admin.accounts.index') }}" class="text-gray-500 hover:text-gray-900">NovaCloud API</a>
    <span class="mx-2 text-gray-300">/</span>
    <span class="text-gray-900">Sửa</span>
@endsection

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Cập nhật NovaCloud Account</h1>
    <p class="text-sm text-gray-500 mt-1">{{ $account->label }}</p>
</div>

<div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.accounts.update', $account) }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Tên hiển thị</label>
                <input type="text" name="label" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm" required value="{{ old('label', $account->label) }}">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Ưu tiên</label>
                <input type="number" name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm" value="{{ old('priority', $account->priority) }}">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">API Username</label>
            <input type="email" name="api_username" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm" required value="{{ old('api_username', $account->api_username) }}">
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">API App Key</label>
            <input type="text" name="api_app" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm font-mono" required value="{{ old('api_app', $account->api_app) }}">
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">API Secret Key mới</label>
            <input type="password" name="api_secret" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm font-mono">
            <p class="text-[11px] text-gray-500 mt-1">Để trống nếu không đổi secret.</p>
        </div>

        <div class="flex items-center gap-6 pt-4 border-t border-gray-100">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" class="w-4 h-4 text-cloud-600 border-gray-300 rounded" {{ old('is_active', $account->is_active) ? 'checked' : '' }}>
                <span class="text-sm font-semibold text-gray-700">Active</span>
            </label>

            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_full" value="1" class="w-4 h-4 text-red-600 border-gray-300 rounded" {{ old('is_full', $account->is_full) ? 'checked' : '' }}>
                <span class="text-sm font-semibold text-gray-700">Full / tạm dừng cấp phát</span>
            </label>
        </div>

        <div class="pt-6 border-t border-gray-100 flex items-center justify-between">
            <button type="button" onclick="showGlobalConfirm('Xóa account này?', () => document.getElementById('delete-account-form').submit())" class="px-4 py-2 border border-red-200 rounded-md bg-red-50 text-red-600 font-medium text-sm">
                Xóa account
            </button>

            <div class="flex gap-3">
                <a href="{{ route('admin.accounts.index') }}" class="px-4 py-2 border border-gray-300 rounded-md bg-white text-gray-700 font-medium text-sm">Hủy</a>
                <button type="submit" class="px-4 py-2 rounded-md bg-cloud-600 text-white font-medium text-sm">
                    Lưu
                </button>
            </div>
        </div>
    </form>

    <form id="delete-account-form" action="{{ route('admin.accounts.destroy', $account) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection
