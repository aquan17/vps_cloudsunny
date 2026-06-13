<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f8fafc">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🌊</text></svg>">
    <title>@yield('title', 'SeaServer — VPS SeaServer')</title>
    <meta name="description" content="@yield('meta', 'Thuê VPS SeaServer tự động')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    {{-- Tailwind CSS via CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        cloud: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#0052cc', // CloudEngine primary blue
                            700: '#0043a8',
                        }
                    }
                }
            }
        }
    </script>
    
    {{-- Custom overrides (nếu cần) --}}
    <style>
        body { background-color: #f8fafc; } /* Light gray bg */
        [x-cloak] { display: none !important; }
        
        /* Custom scrollbar for sidebar */
        .custom-scroll::-webkit-scrollbar { width: 4px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    </style>
    
    @stack('head')
</head>
<body class="text-gray-800 antialiased bg-[#f8fafc] font-sans flex min-h-screen">

    {{-- ===== MOBILE BACKDROP ===== --}}
    <div id="sidebarBackdrop" class="fixed inset-0 bg-gray-900/50 z-40 hidden md:hidden transition-opacity"></div>

    {{-- ===== SIDEBAR (Left) ===== --}}
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 flex flex-col h-screen transform -translate-x-full md:translate-x-0 transition-transform duration-200 ease-in-out">
        {{-- Brand / Logo --}}
        <div class="h-16 flex items-center px-6 border-b border-gray-100">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-xl font-bold text-gray-900 tracking-tight">
                <span class="text-cloud-600">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-16.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a4.5 4.5 0 01.9-2.7L5.737 5.1a3.375 3.375 0 012.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 01.9 2.7m0 0a3 3 0 01-3 3m0 3h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008zm-3 6h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008z" />
                    </svg>
                </span>
                SeaServer
            </a>
        </div>

        {{-- Deploy Button --}}
        <div class="p-4">
            <a href="{{ route('pricing') }}" class="w-full flex items-center justify-center gap-2 bg-cloud-600 hover:bg-cloud-700 text-white font-medium py-2 px-4 rounded-md transition-colors text-sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Khởi tạo VPS
            </a>
        </div>

        {{-- Nav Links --}}
        <nav class="flex-1 px-3 py-2 space-y-1 overflow-y-auto custom-scroll">
            {{-- <a href="{{ route('home') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors 
               {{ request()->routeIs('home') ? 'bg-cloud-50 text-cloud-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="opacity-70"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg>
                Dashboard
            </a> --}}
            
            @auth
            <a href="{{ route('dashboard') }}" 
               class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition-colors
               {{ request()->routeIs('dashboard*') ? 'bg-cloud-50 text-cloud-700 border-l-2 border-cloud-600 -ml-3 pl-[14px]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-md' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="opacity-70"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15a4.5 4.5 0 004.5 4.5H18a3.75 3.75 0 001.332-7.257 3 3 0 00-3.758-3.848 5.25 5.25 0 00-10.233 2.33A4.502 4.502 0 002.25 15z" /></svg>
                Máy chủ VPS
            </a>

            <a href="{{ route('proxy.index') }}" 
               class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition-colors
               {{ request()->routeIs('proxy.index') || request()->routeIs('proxy.show') ? 'bg-cloud-50 text-cloud-700 border-l-2 border-cloud-600 -ml-3 pl-[14px]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-md' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="opacity-70"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.974 0-5.699-.588-8.083-1.582" /></svg>
                Dịch vụ Proxy
            </a>
            
            <a href="{{ route('proxy.store.index') }}" 
               class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition-colors
               {{ request()->routeIs('proxy.store*') ? 'bg-cloud-50 text-cloud-700 border-l-2 border-cloud-600 -ml-3 pl-[14px]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-md' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="opacity-70"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" /></svg>
                Mua Proxy Mới
            </a>
            
            <a href="{{ route('topup.index') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors
               {{ request()->routeIs('topup*') ? 'bg-cloud-50 text-cloud-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="opacity-70"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" /></svg>
                Nạp tiền & Thanh toán
            </a>

            <a href="{{ route('profile.index') }}" 
               class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition-colors
               {{ request()->routeIs('profile*') ? 'bg-cloud-50 text-cloud-700 border-l-2 border-cloud-600 -ml-3 pl-[14px]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-md' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="opacity-70"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                Hồ sơ cá nhân
            </a>
            @endauth

            @if(auth()->check() && auth()->user()->isAdmin())
            <div class="pt-4 mt-4 border-t border-gray-100">
                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Khu vực Quản trị</p>
                <a href="{{ route('admin.accounts.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('admin.accounts.*') ? 'bg-gray-100 text-gray-900' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="opacity-70"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                    SeaServer API
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('admin.users.*') ? 'bg-gray-100 text-gray-900' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="opacity-70"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                    Người dùng
                </a>
                <a href="{{ route('admin.instances.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('admin.instances.*') ? 'bg-gray-100 text-gray-900' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="opacity-70"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-16.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a4.5 4.5 0 01.9-2.7L5.737 5.1a3.375 3.375 0 012.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 01.9 2.7m0 0a3 3 0 01-3 3m0 3h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008zm-3 6h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008z" /></svg>
                    QL VPS
                </a>
                <a href="{{ route('admin.revenue.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('admin.revenue.*') ? 'bg-gray-100 text-gray-900' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="opacity-70"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Doanh thu & Lãi
                </a>

            </div>
            @endif
        </nav>

        {{-- Footer area of sidebar --}}
        <div class="p-3 border-t border-gray-100 space-y-1">
            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="opacity-70"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" /></svg>
                Hỗ trợ
            </a>
            @auth
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="opacity-70"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" /></svg>
                    Đăng xuất
                </button>
            </form>
            @endauth
        </div>
    </aside>

    {{-- ===== MAIN WRAPPER ===== --}}
    <div class="flex-1 flex flex-col min-w-0 min-h-screen md:ml-64">
        
        {{-- TOP HEADER --}}
        <header class="h-16 bg-white flex items-center justify-between px-6 border-b border-gray-200 flex-shrink-0 z-10">
            {{-- Mobile Menu Toggle & Breadcrumbs --}}
            <div class="flex items-center gap-4">
                <button id="mobileMenuBtn" class="md:hidden text-gray-500 hover:text-gray-900 focus:outline-none">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                </button>
                <div class="hidden sm:flex items-center text-sm font-medium text-gray-500">
                    @yield('breadcrumbs', 'CloudEngine Platform')
                </div>
            </div>

            {{-- Right Header Actions --}}
            <div class="flex items-center gap-6">
                <div class="hidden lg:flex items-center gap-4 text-sm font-medium text-gray-600">
                    <a href="#" class="hover:text-gray-900 transition-colors">Tài liệu</a>
                    <a href="#" class="hover:text-gray-900 transition-colors">API</a>
                    <a href="#" class="hover:text-gray-900 transition-colors">Cộng đồng</a>
                </div>
                
                <div class="flex items-center gap-4 border-l border-gray-200 pl-6">
                    <button class="text-gray-400 hover:text-gray-600 relative">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                    </button>
                    
                    @auth
                    {{-- Balance Display --}}
                    <a href="{{ route('topup.index') }}" class="flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-md text-sm font-semibold text-gray-700 transition-colors">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" /></svg>
                        {{ number_format(auth()->user()->balance) }} đ
                    </a>
                    
                    {{-- Avatar --}}
                    <a href="{{ route('profile.index') }}" class="w-8 h-8 bg-cloud-600 hover:bg-cloud-700 transition-colors text-white rounded-full flex items-center justify-center font-bold text-xs uppercase cursor-pointer shadow-sm">
                        {{ substr(auth()->user()->email, 0, 2) }}
                    </a>
                    @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Đăng nhập</a>
                    @endauth
                </div>
            </div>
        </header>

        {{-- MAIN CONTENT AREA (Scrollable) --}}
        <main class="flex-1 custom-scroll">
            <div class="p-6 md:p-8 max-w-7xl mx-auto">
                
                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-md bg-green-50 border border-green-200 flex items-start gap-3">
                        <svg class="text-green-600 mt-0.5 flex-shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span class="text-sm text-green-800 font-medium">{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-6 p-4 rounded-md bg-red-50 border border-red-200 flex items-start gap-3">
                        <svg class="text-red-600 mt-0.5 flex-shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span class="text-sm text-red-800 font-medium">{{ session('error') }}</span>
                    </div>
                @endif
                
                {{-- Injected View --}}
                @yield('content')
                
            </div>
        </main>
        
    </div>

    @stack('scripts')
    <script>
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const toggleBtn = document.getElementById('mobileMenuBtn');
        let isSidebarOpen = false;

        function toggleSidebar() {
            isSidebarOpen = !isSidebarOpen;
            if (isSidebarOpen) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }

        if(toggleBtn) toggleBtn.addEventListener('click', toggleSidebar);
        if(backdrop) backdrop.addEventListener('click', toggleSidebar);
    </script>

    {{-- Floating Zalo Button --}}
    {{-- <a href="https://zalo.me/0862579104" target="_blank" rel="noopener noreferrer" class="fixed bottom-6 right-6 z-50 flex items-center justify-center w-14 h-14 rounded-full shadow-[0_4px_20px_rgba(0,104,255,0.4)] hover:shadow-[0_6px_25px_rgba(0,104,255,0.6)] transition-all duration-300 hover:scale-110 group bg-white"> --}}
    <a href="https://zalo.me/g/bh9hrrcujly5ab2pqmfj" target="_blank" rel="noopener noreferrer" class="fixed bottom-6 right-6 z-50 flex items-center justify-center w-14 h-14 rounded-full shadow-[0_4px_20px_rgba(0,104,255,0.4)] hover:shadow-[0_6px_25px_rgba(0,104,255,0.6)] transition-all duration-300 hover:scale-110 group bg-white">
        <span class="absolute w-full h-full rounded-full bg-[#0068FF] animate-ping opacity-40"></span>
        <img src="{{ asset('image/zalo.svg') }}" alt="Zalo" class="relative z-10 w-full h-full rounded-full object-cover">
    </a>

    {{-- Global Confirm Modal --}}
    <div id="globalConfirmModal" class="fixed inset-0 z-[100] hidden flex-col items-center justify-center">
        <!-- Backdrop -->
        <div id="globalConfirmBackdrop" class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity opacity-0 cursor-pointer" onclick="closeGlobalConfirm()"></div>
        
        <!-- Card -->
        <div id="globalConfirmCard" class="relative bg-white rounded-xl shadow-2xl w-full max-w-sm p-6 transform scale-95 opacity-0 transition-all duration-200">
            <div class="flex items-start gap-4 mb-5">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0 text-red-600 mt-1">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Xác nhận</h3>
                    <p id="globalConfirmMessage" class="text-sm text-gray-600 leading-relaxed"></p>
                </div>
            </div>
            
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeGlobalConfirm()" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Hủy bỏ
                </button>
                <button type="button" id="globalConfirmBtn" class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 transition-colors shadow-sm">
                    Đồng ý
                </button>
            </div>
        </div>
    </div>

    {{-- Global Full Screen Loader --}}
    <div id="globalFullScreenLoader" class="fixed inset-0 z-[110] flex-col items-center justify-center bg-white/80 backdrop-blur-sm hidden">
        <div class="text-center bg-white p-8 rounded-xl shadow-2xl border border-gray-100 max-w-sm w-full mx-4 transform transition-all">
            <div class="w-16 h-16 mx-auto mb-5 rounded-full border-4 border-cloud-100 border-t-cloud-600 animate-spin"></div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Đang xử lý yêu cầu...</h3>
            <p class="text-sm text-gray-500">Vui lòng đợi giây lát và không đóng trình duyệt lúc này.</p>
        </div>
    </div>

    <script>
        let currentConfirmCallback = null;

        // Custom Confirm Function
        window.showGlobalConfirm = function(message, callback) {
            currentConfirmCallback = callback;
            document.getElementById('globalConfirmMessage').innerHTML = message.replace(/\n/g, '<br>');
            
            const modal = document.getElementById('globalConfirmModal');
            const backdrop = document.getElementById('globalConfirmBackdrop');
            const card = document.getElementById('globalConfirmCard');
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            // Trigger animation
            setTimeout(() => {
                backdrop.classList.remove('opacity-0');
                backdrop.classList.add('opacity-100');
                card.classList.remove('opacity-0', 'scale-95');
                card.classList.add('opacity-100', 'scale-100');
            }, 10);
        };

        window.closeGlobalConfirm = function() {
            const backdrop = document.getElementById('globalConfirmBackdrop');
            const card = document.getElementById('globalConfirmCard');
            
            backdrop.classList.remove('opacity-100');
            backdrop.classList.add('opacity-0');
            card.classList.remove('opacity-100', 'scale-100');
            card.classList.add('opacity-0', 'scale-95');
            
            setTimeout(() => {
                const modal = document.getElementById('globalConfirmModal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                currentConfirmCallback = null;
            }, 200);
        };

        document.getElementById('globalConfirmBtn').addEventListener('click', function() {
            if (currentConfirmCallback) {
                currentConfirmCallback();
            }
            closeGlobalConfirm();
        });

        // Global interceptor for form submissions (Confirm + Loading State)
        document.addEventListener('submit', function(e) {
            const form = e.target;
            
            // Prevent double submit
            if (form.dataset.submitted === 'true') {
                e.preventDefault();
                return;
            }

            if (form && form.hasAttribute('data-confirm')) {
                e.preventDefault();
                const msg = form.getAttribute('data-confirm');
                showGlobalConfirm(msg, function() {
                    form.removeAttribute('data-confirm');
                    setFormLoadingState(form);
                    setTimeout(() => form.submit(), 50);
                });
                return;
            }
            
            // If no confirm, prevent default, set state and submit
            e.preventDefault();
            setFormLoadingState(form);
            setTimeout(() => form.submit(), 50);
        });

        function setFormLoadingState(form) {
            form.dataset.submitted = 'true';
            
            // Hiện hiệu ứng Full-Screen Loader cho an toàn tuyệt đối
            const loader = document.getElementById('globalFullScreenLoader');
            if (loader) {
                loader.classList.remove('hidden');
                loader.classList.add('flex');
            }

            const btn = form.querySelector('button[type="submit"]');
            if (btn) {
                // Prevent width jumping if possible
                if (btn.offsetWidth > 0) {
                    btn.style.width = btn.offsetWidth + 'px';
                }
                btn.disabled = true;
                btn.classList.add('opacity-75', 'cursor-not-allowed');
                btn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Đang xử lý...';
            }
        }
    </script>
</body>
</html>
