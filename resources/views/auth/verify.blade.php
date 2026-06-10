<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận Email — NovaCloud</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        cloud: { 50: '#eff6ff', 500: '#3b82f6', 600: '#0052cc', 700: '#0043a8' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4 font-sans text-gray-800">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 border border-gray-100 text-center relative overflow-hidden">
        {{-- Decorative background element --}}
        <div class="absolute top-0 left-0 w-full h-2 bg-cloud-600"></div>

        <div class="w-20 h-20 bg-cloud-50 rounded-full flex items-center justify-center mx-auto mb-6 text-cloud-600">
            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
        </div>

        <h1 class="text-2xl font-bold text-gray-900 mb-3 tracking-tight">Xác thực địa chỉ Email</h1>
        
        <p class="text-gray-500 text-sm mb-8 leading-relaxed">
            Cảm ơn bạn đã đăng ký! Để bảo mật thông tin và bắt đầu sử dụng dịch vụ thuê VPS, vui lòng nhấp vào liên kết xác nhận mà chúng tôi vừa gửi đến địa chỉ email của bạn.
        </p>

        @if (session('success'))
            <div class="bg-green-50 text-green-700 p-3 rounded-md text-sm font-medium border border-green-100 mb-6 flex items-start gap-2 text-left">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        
        @if (session('error'))
            <div class="bg-red-50 text-red-700 p-3 rounded-md text-sm font-medium border border-red-100 mb-6 flex items-start gap-2 text-left">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="space-y-4">
            <form method="POST" action="{{ route('verification.resend') }}">
                @csrf
                <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-cloud-600 hover:bg-cloud-700 focus:outline-none transition-colors">
                    Gửi lại Email xác nhận
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">
                    Đăng xuất
                </button>
            </form>
        </div>
    </div>
</body>
</html>
