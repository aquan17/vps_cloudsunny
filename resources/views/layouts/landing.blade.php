<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#ffffff">
    <title>@yield('title', 'NovaCloud — Infrastructure for the next generation')</title>
    <meta name="description" content="@yield('meta', 'Hạ tầng Cloud hiệu năng cao, tối ưu cho tốc độ và độ ổn định. Triển khai toàn cầu trong vài giây với thanh toán VNĐ linh hoạt.')">
    
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
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        body { background-color: #f8fafc; }
        .gradient-text {
            background: linear-gradient(to right, #0052cc, #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="text-gray-800 antialiased font-sans min-h-screen flex flex-col">

    {{-- Top Navigation --}}
    <header class="bg-white/80 backdrop-blur-md border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                {{-- Logo & Left Nav --}}
                <div class="flex items-center gap-8">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                        <div class="w-8 h-8 rounded-lg bg-cloud-600 flex items-center justify-center text-white shadow-sm group-hover:bg-cloud-700 transition-colors">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" /></svg>
                        </div>
                        <span class="text-lg font-bold text-gray-900 tracking-tight">NovaCloud</span>
                    </a>
                    
                    <nav class="hidden md:flex gap-6">
                        <a href="#" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">Tài liệu</a>
                        <a href="#" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">API</a>
                        <a href="#" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">Cộng đồng</a>
                    </nav>
                </div>

                {{-- Right Nav --}}
                <div class="flex items-center gap-4">
                    @guest
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="hidden sm:inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white bg-cloud-600 rounded-md hover:bg-cloud-700 transition-colors shadow-sm">
                            Khởi tạo VPS
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="hidden sm:inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white bg-cloud-600 rounded-md hover:bg-cloud-700 transition-colors shadow-sm">
                            Quản lý VPS
                        </a>
                    @endguest
                    
                    {{-- Mobile menu button --}}
                    <button class="md:hidden p-2 text-gray-500 hover:text-gray-900">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="flex-grow">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t border-gray-200 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
            <div class="md:flex md:items-center md:justify-between flex-col md:flex-row gap-6">
                <div class="flex justify-center md:justify-start">
                    <p class="text-xs text-gray-500 font-medium">
                        &copy; {{ date('Y') }} NovaCloud Infrastructure. Bản quyền đã được bảo hộ.
                    </p>
                </div>
                <div class="flex justify-center gap-6">
                    <a href="#" class="text-xs text-gray-500 hover:text-gray-900 transition-colors">Chính sách bảo mật</a>
                    <a href="#" class="text-xs text-gray-500 hover:text-gray-900 transition-colors">Điều khoản dịch vụ</a>
                    <a href="#" class="text-xs text-gray-500 hover:text-gray-900 transition-colors">Trạng thái hệ thống</a>
                    <a href="#" class="text-xs text-gray-500 hover:text-gray-900 transition-colors">Hỗ trợ</a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
