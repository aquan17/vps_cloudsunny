@extends('layouts.app')
@section('title', 'Quản lý Người dùng — Admin')

@section('breadcrumbs')
    <span>Quản trị</span>
    <span class="mx-2 text-gray-300">/</span>
    <span class="text-gray-900">Người dùng</span>
@endsection

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Quản lý Người dùng</h1>
        <p class="text-sm text-gray-500 mt-1">Tổng cộng: {{ $users->total() }} tài khoản đã đăng ký.</p>
    </div>

</div>



<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden flex flex-col mb-6">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-80">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm theo tên hoặc email..." class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:border-cloud-600 focus:ring-1 focus:ring-cloud-600 bg-white shadow-sm">
            </div>
            <button type="submit" class="px-4 py-2 border border-gray-300 rounded-md bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm transition-colors flex-shrink-0">
                Tìm kiếm
            </button>
        </form>
    </div>

    <div class="hidden lg:block overflow-x-auto flex-1">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">#ID</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Tên / Email</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Số dư</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">VPS</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Vai trò</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Ngày đăng ký</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($users as $u)
            <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400 font-mono">
                    #{{ $u->id }}
                </td>
                <td class="px-6 py-4">
                    <div class="font-bold text-gray-900 text-sm mb-0.5">{{ $u->name }}</div>
                    <div class="text-xs text-gray-500">{{ $u->email }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-bold font-mono {{ $u->balance > 0 ? 'text-cloud-600' : 'text-gray-400' }}">
                        {{ number_format($u->balance) }} đ
                    </div>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                        {{ $u->vps_instances_count }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    @if($u->is_admin)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">Admin</span>
                    @else
                        <span class="text-xs text-gray-500 font-medium">User</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                    {{ $u->created_at->format('d/m/Y') }}
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('admin.users.show', $u) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-gray-300 rounded-md text-xs font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-colors shadow-sm">
                            Xem chi tiết
                        </a>
                        <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng này không? Hành động này không thể hoàn tác.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-red-200 rounded-md text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 transition-colors shadow-sm">
                                Xóa
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                    @if(request('search'))
                        <p class="text-gray-900 font-semibold mb-1">Không tìm thấy kết quả</p>
                        <p>Không có người dùng nào khớp với từ khóa "{{ request('search') }}".</p>
                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 mt-4 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-1.5 px-3 rounded-md transition-colors text-sm shadow-sm">
                            Xóa tìm kiếm
                        </a>
                    @else
                        <p>Không có người dùng nào.</p>
                    @endif
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- MOBILE BOX VIEW --}}
    <div class="block lg:hidden divide-y divide-gray-100">
        @foreach($users as $u)
        <div class="p-4 hover:bg-gray-50/50 transition-colors">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <div class="font-bold text-gray-900 text-sm mb-0.5">
                        #{{ $u->id }} - {{ $u->name }}
                    </div>
                    <div class="text-xs text-gray-500">{{ $u->email }}</div>
                </div>
                @if($u->is_admin)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Admin</span>
                @else
                    <span class="text-xs text-gray-500 font-medium">User</span>
                @endif
            </div>

            <div class="flex justify-between items-end mt-3 border-t border-gray-100 pt-3">
                <div class="space-y-1">
                    <div class="text-xs text-gray-500">Số dư: <span class="font-bold font-mono text-sm {{ $u->balance > 0 ? 'text-cloud-600' : 'text-gray-900' }}">{{ number_format($u->balance) }} đ</span></div>
                    <div class="text-xs text-gray-500">VPS đang chạy: <span class="font-bold text-gray-900">{{ $u->vps_instances_count }}</span></div>
                    <div class="text-xs text-gray-500 font-mono">{{ $u->created_at->format('d/m/Y') }}</div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.users.show', $u) }}" class="inline-block text-cloud-600 hover:text-cloud-800 font-semibold text-xs px-3 py-1.5 border border-cloud-200 rounded bg-cloud-50">Xem chi tiết</a>
                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng này không?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-block text-red-600 hover:text-red-800 font-semibold text-xs px-3 py-1.5 border border-red-200 rounded bg-red-50">Xóa</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div>
    {{ $users->links() }}
</div>
@endsection
