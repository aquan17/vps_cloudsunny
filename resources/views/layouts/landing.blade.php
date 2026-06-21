<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#ffffff">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🌊</text></svg>">
    <title>@yield('title', 'SeaServer — Infrastructure for the next generation')</title>
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
                        <span class="text-lg font-bold text-gray-900 tracking-tight">SeaServer</span>
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
                        <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 hover:text-gray-900 transition-colors">
                            Đăng nhập
                        </a>
                        <a href="{{ route('register') }}" class="hidden sm:inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white bg-cloud-600 rounded-md hover:bg-cloud-700 transition-colors shadow-sm">
                            Đăng ký
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

    <footer class="bg-white border-t border-gray-200 mt-20 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div class="md:col-span-1">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 mb-4 group">
                        <div class="w-8 h-8 rounded-lg bg-cloud-600 flex items-center justify-center text-white shadow-sm">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" /></svg>
                        </div>
                        <span class="text-xl font-bold text-gray-900 tracking-tight">SeaServer</span>
                    </a>
                    <p class="text-sm text-gray-500 leading-relaxed mb-6">
                        SeaServer cung cấp giải pháp hạ tầng Cloud VPS và Proxy IPv4 với hiệu suất mạnh mẽ, đáp ứng tối đa nhu cầu của cá nhân và doanh nghiệp.
                    </p>
                    <div class="flex gap-4">
                        <a href="#" class="text-gray-400 hover:text-cloud-600 transition-colors">
                            <span class="sr-only">Facebook</span>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" /></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-cloud-600 transition-colors">
                            <span class="sr-only">Twitter</span>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" /></svg>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Sản Phẩm</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-sm text-gray-600 hover:text-cloud-600 transition-colors">Cloud VPS NVMe</a></li>
                        <li><a href="#" class="text-sm text-gray-600 hover:text-cloud-600 transition-colors">Proxy IPv4 Tĩnh</a></li>
                        <li><a href="#" class="text-sm text-gray-600 hover:text-cloud-600 transition-colors">Bảng giá</a></li>
                        <li><a href="#" class="text-sm text-gray-600 hover:text-cloud-600 transition-colors">API cho Developer</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Hỗ Trợ</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-sm text-gray-600 hover:text-cloud-600 transition-colors">Tài liệu hướng dẫn</a></li>
                        <li><a href="#" class="text-sm text-gray-600 hover:text-cloud-600 transition-colors">Câu hỏi thường gặp (FAQ)</a></li>
                        <li><a href="#" class="text-sm text-gray-600 hover:text-cloud-600 transition-colors">Trạng thái hệ thống</a></li>
                        <li><a href="#" class="text-sm text-gray-600 hover:text-cloud-600 transition-colors">Liên hệ kỹ thuật</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Công Ty</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-sm text-gray-600 hover:text-cloud-600 transition-colors">Giới thiệu</a></li>
                        <li><a href="#" class="text-sm text-gray-600 hover:text-cloud-600 transition-colors">Khách hàng</a></li>
                        <li><a href="#" class="text-sm text-gray-600 hover:text-cloud-600 transition-colors">Điều khoản dịch vụ</a></li>
                        <li><a href="#" class="text-sm text-gray-600 hover:text-cloud-600 transition-colors">Chính sách bảo mật</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-200 pt-8 flex flex-col items-center justify-center gap-4">
                <p class="text-xs text-gray-500 font-medium text-center">
                    &copy; {{ date('Y') }} SeaServer Infrastructure. Bản quyền đã được bảo hộ.
                </p>
            </div>
            </div>
        </div>
    </footer>

</body>
</html>
