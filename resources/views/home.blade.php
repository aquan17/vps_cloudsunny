@extends('layouts.landing')
@section('title', 'SeaServer — Infrastructure for the next generation')

@section('content')

{{-- ══ HERO SECTION ══ --}}
<section class="relative pt-20 pb-24 lg:pt-32 lg:pb-36 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="lg:grid lg:grid-cols-12 lg:gap-16 items-center">
            
            {{-- Text Content --}}
            <div class="lg:col-span-6 text-center lg:text-left mb-16 lg:mb-0">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 border border-blue-100 text-cloud-600 text-[11px] font-bold tracking-wide uppercase mb-6 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-cloud-500 animate-pulse"></span>
                    Mới: Hạ tầng Cloud siêu tốc
                </div>
                
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 tracking-tight leading-[1.1] mb-6">
                    Hạ tầng Cloud cho<br>
                    <span class="gradient-text">thế hệ tương lai.</span>
                </h1>
                
                <p class="text-lg sm:text-xl text-gray-600 mb-8 max-w-2xl mx-auto lg:mx-0 leading-relaxed font-medium">
                    VPS hiệu năng cao tối ưu cho tốc độ, độ ổn định tuyệt đối. Triển khai toàn cầu chỉ trong vài giây.
                </p>
                
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                    <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="w-full sm:w-auto px-8 py-3.5 bg-cloud-600 hover:bg-cloud-700 text-white text-sm font-bold rounded-md shadow-lg shadow-cloud-500/30 transition-all hover:-translate-y-0.5">
                        Deploy Now
                    </a>
                    <a href="#plans" class="w-full sm:w-auto px-8 py-3.5 bg-white border border-gray-300 hover:border-gray-400 text-gray-700 hover:bg-gray-50 text-sm font-bold rounded-md shadow-sm transition-all flex items-center justify-center gap-2">
                        Xem bảng giá
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                    </a>
                </div>
            </div>

            {{-- Terminal Mockup --}}
            <div class="lg:col-span-6 relative">
                <div class="absolute -inset-0.5 bg-gradient-to-r from-cloud-500 to-blue-400 rounded-2xl blur opacity-20"></div>
                <div class="relative bg-[#0f172a] rounded-xl shadow-2xl border border-gray-800 overflow-hidden">
                    <div class="flex items-center px-4 py-3 bg-[#1e293b] border-b border-gray-800">
                        <div class="flex gap-2">
                            <div class="w-3 h-3 rounded-full bg-red-500"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                            <div class="w-3 h-3 rounded-full bg-green-500"></div>
                        </div>
                        <div class="mx-auto text-[11px] font-mono text-gray-400">root@SeaServer:~</div>
                    </div>
                    <div class="p-6 font-mono text-sm leading-relaxed text-gray-300 min-h-[250px] relative" id="terminal-body">
                        <div id="term-content"></div>
                        <div class="flex items-start gap-2 mt-1 hidden" id="term-input-line">
                            <span class="text-cloud-400">❯</span>
                            <span class="text-gray-100" id="term-typing"></span>
                            <span class="text-gray-400 animate-pulse" id="term-cursor">_</span>
                        </div>
                    </div>
                    
                    <script>
                        document.addEventListener("DOMContentLoaded", () => {
                            const contentEl = document.getElementById('term-content');
                            const inputLine = document.getElementById('term-input-line');
                            const typingEl = document.getElementById('term-typing');
                            
                            const commands = [
                                {
                                    cmd: "ssh root@SeaServer-sg1",
                                    out: `<div class="text-gray-500 mb-1">Authenticating with public key...</div>
                                          <div class="text-gray-100 mb-1">Welcome to SeaServer Linux 22.04 LTS</div>
                                          <div class="text-gray-400 mb-2">System load: 0.01  Memory: 4%</div>`
                                },
                                {
                                    cmd: "apt update && apt upgrade -y",
                                    out: `<div class="text-gray-400 mb-1">Reading package lists... Done</div>
                                          <div class="text-gray-400 mb-1">Building dependency tree... Done</div>
                                          <div class="text-green-400 mb-2">All packages are up to date.</div>`
                                },
                                {
                                    cmd: "docker run -d -p 80:80 nginx",
                                    out: `<div class="text-gray-400 mb-1">Unable to find image 'nginx:latest' locally</div>
                                          <div class="text-gray-400 mb-1">latest: Pulling from library/nginx</div>
                                          <div class="text-gray-100 mb-2">Status: Downloaded newer image for nginx:latest</div>`
                                }
                            ];
                            
                            let currentCmdIdx = 0;
                            
                            async function typeWriter(text, element, speed = 50) {
                                element.textContent = '';
                                for (let i = 0; i < text.length; i++) {
                                    element.textContent += text.charAt(i);
                                    await new Promise(r => setTimeout(r, speed + Math.random() * 50));
                                }
                            }
                            
                            async function runSequence() {
                                // Clear screen
                                contentEl.innerHTML = '';
                                currentCmdIdx = 0;
                                
                                while(currentCmdIdx < commands.length) {
                                    inputLine.classList.remove('hidden');
                                    typingEl.textContent = '';
                                    
                                    // Wait before typing
                                    await new Promise(r => setTimeout(r, 800));
                                    
                                    // Type command
                                    await typeWriter(commands[currentCmdIdx].cmd, typingEl);
                                    
                                    // Wait for enter key
                                    await new Promise(r => setTimeout(r, 400));
                                    
                                    // Append command to history
                                    contentEl.innerHTML += `<div class="flex items-start gap-2 mb-1"><span class="text-cloud-400">❯</span><span class="text-gray-100">${commands[currentCmdIdx].cmd}</span></div>`;
                                    inputLine.classList.add('hidden');
                                    
                                    // Simulate network delay
                                    await new Promise(r => setTimeout(r, 300));
                                    
                                    // Show output
                                    contentEl.innerHTML += `<div class="animate-fade-in">${commands[currentCmdIdx].out}</div>`;
                                    
                                    // Wait before next command
                                    await new Promise(r => setTimeout(r, 1500));
                                    
                                    currentCmdIdx++;
                                }
                                
                                // End of commands, wait longer then restart
                                inputLine.classList.remove('hidden');
                                typingEl.textContent = 'clear';
                                await new Promise(r => setTimeout(r, 600));
                                await typeWriter('clear', typingEl);
                                await new Promise(r => setTimeout(r, 400));
                                
                                // Loop
                                runSequence();
                            }
                            
                            // Start
                            setTimeout(runSequence, 500);
                        });
                    </script>
                    
                    <style>
                        .animate-fade-in { animation: fadeIn 0.3s ease-in-out forwards; }
                        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
                    </style>

        </div>
    </div>
</section>

{{-- ══ TRUSTED SECTION ══ --}}
<section class="border-y border-gray-100 bg-white py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <p class="text-center text-xs font-bold text-gray-400 uppercase tracking-widest mb-6">Được tin dùng bởi các nhà phát triển với các công nghệ hàng đầu</p>
        <div class="flex flex-wrap justify-center gap-8 md:gap-16 opacity-50 grayscale hover:grayscale-0 transition-all duration-500">
            <span class="text-xl font-bold font-sans text-gray-800">Ubuntu</span>
            <span class="text-xl font-bold font-sans text-gray-800">Debian</span>
            <span class="text-xl font-bold font-sans text-gray-800">CentOS</span>
            <span class="text-xl font-bold font-sans text-gray-800">Docker</span>
            <span class="text-xl font-bold font-sans text-gray-800">Kubernetes</span>
        </div>
    </div>
</section>

{{-- ══ FEATURES SECTION ══ --}}
<section class="py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Tại sao chọn SeaServer?</h2>
            <p class="mt-4 text-lg text-gray-600">Những lợi thế khác biệt dành riêng cho bạn.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-cloud-600 mb-6">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Tự động hóa 100%</h3>
                <p class="text-sm text-gray-600 leading-relaxed">Khởi tạo, tắt, bật, cấu hình IP hay re-install HĐH hoàn toàn tự động không cần can thiệp thủ công.</p>
            </div>
            
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-cloud-600 mb-6">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Thanh toán VNĐ</h3>
                <p class="text-sm text-gray-600 leading-relaxed">Không cần thẻ tín dụng quốc tế Visa/Mastercard. Dễ dàng nạp tiền và thanh toán qua tài khoản ngân hàng nội địa.</p>
            </div>
            
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-cloud-600 mb-6">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Uptime 99.9%</h3>
                <p class="text-sm text-gray-600 leading-relaxed">Sử dụng hạ tầng thật từ Akamai Connected Cloud, đảm bảo hiệu suất mạnh mẽ và độ ổn định cao nhất cho ứng dụng của bạn.</p>
            </div>
        </div>
    </div>
</section>

{{-- ══ PRICING SECTION ══ --}}
<section id="plans" class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">High-Performance Plans</h2>
            <p class="mt-4 text-lg text-gray-600">Bảng giá đơn giản, dễ hiểu cho các dự án production.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            @php 
                // Lấy 3 gói đầu tiên để hiển thị (hoặc lấy tất cả nếu muốn)
                $displayPlans = array_slice($plans, 0, 3, true); 
                $count = 0;
            @endphp
            @foreach($displayPlans as $pid => $p)
                @php 
                    $count++;
                    $isPopular = ($count == 2); // Highlight the middle plan
                @endphp
                <div class="relative bg-white rounded-2xl p-8 flex flex-col
                    {{ $isPopular ? 'border-2 border-cloud-600 shadow-xl scale-105 z-10' : 'border border-gray-200 shadow-sm' }}">
                    
                    @if($isPopular)
                        <div class="absolute -top-3 inset-x-0 flex justify-center">
                            <span class="bg-cloud-600 text-white text-[10px] font-bold uppercase tracking-widest py-1 px-3 rounded-full shadow-sm">
                                Most Popular
                            </span>
                        </div>
                    @endif

                    <h3 class="text-xl font-bold text-gray-900">{{ $p['name'] }}</h3>
                    <p class="text-sm text-gray-500 mt-2">Dành cho các ứng dụng {{ $isPopular ? 'chuyên nghiệp' : 'vừa và nhỏ' }}.</p>
                    
                    <div class="mt-6 mb-8 flex items-baseline text-gray-900">
                        @php $displayPrice = (!empty($p['on_sale'])) ? $p['sale_price_per_month'] : $p['price_per_month']; @endphp
                        <span class="text-4xl font-extrabold tracking-tight">{{ number_format($displayPrice) }}</span>
                        @if(!empty($p['on_sale']))
                            <span class="ml-2 text-sm text-gray-500 line-through">{{ number_format($p['price_per_month']) }}</span>
                        @endif
                        <span class="ml-1 text-sm font-medium text-gray-500">VNĐ / tháng</span>
                    </div>

                    <ul class="space-y-4 flex-grow mb-8 text-sm text-gray-700">
                        <li class="flex items-center gap-3">
                            <svg class="text-cloud-500" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" /></svg>
                            <span class="font-medium">{{ $p['cores'] }} vCPU (Dedicated)</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="text-cloud-500" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21m-9-1.5h10.5a2.25 2.25 0 002.25-2.25V6.75a2.25 2.25 0 00-2.25-2.25H6.75A2.25 2.25 0 004.5 6.75v10.5a2.25 2.25 0 002.25 2.25z" /></svg>
                            <span class="font-medium">{{ $p['ram'] }} GB RAM</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="text-cloud-500" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" /></svg>
                            <span class="font-medium">{{ $p['disk'] }} GB NVMe</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="text-cloud-500" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" /></svg>
                            <span class="font-medium">{{ $p['transfer_tb'] }} TB Bandwidth</span>
                        </li>
                    </ul>

                    <a href="{{ auth()->check() ? route('store.create', $pid) : route('login') }}" class="block w-full text-center py-2.5 rounded-md text-sm font-bold transition-colors
                        {{ $isPopular ? 'bg-cloud-600 hover:bg-cloud-700 text-white shadow-sm' : 'bg-white border border-gray-300 hover:border-gray-400 text-gray-700 hover:bg-gray-50' }}">
                        Chọn {{ $p['name'] }}
                    </a>
                </div>
            @endforeach
        </div>
        
        <div class="mt-12 text-center">
            <a href="{{ auth()->check() ? route('pricing') : route('login') }}" class="text-sm font-semibold text-cloud-600 hover:text-cloud-700 flex items-center justify-center gap-1">
                Xem toàn bộ bảng giá đầy đủ
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3" /></svg>
            </a>
        </div>
    </div>
</section>

{{-- ══ CTA SECTION ══ --}}
<section class="py-20 bg-gradient-to-br from-cloud-600 to-blue-800 relative overflow-hidden">
    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxwYXRoIGQ9Ik0zNiAzNHYtNGgtMnY0aC00djJoNHY0aDJ2LTRoNHYtMmgtNHptMC0zMFYwaC0ydjRoLTR2Mmg0djRoMnYtNGg0VjRoLTR6TTYgMzR2LTRINHZnLTRIMnY0SDB2MmgydjRoMnYtNGg0em0wLTMwVjBoLTJ2NGgtNHYyaDR2NGgydi00aDRWNGgtNHoiIGZpbGw9IiNmZmZmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSIvPjwvZz48L3N2Zz4=')]"></div>
    <div class="max-w-4xl mx-auto px-4 relative z-10 text-center">
        <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-6 tracking-tight">Sẵn sàng triển khai ứng dụng tiếp theo?</h2>
        <p class="text-blue-100 text-lg mb-10 max-w-2xl mx-auto">Tạo tài khoản và trải nghiệm hiệu năng VPS ấn tượng ngay hôm nay. Triển khai siêu tốc, thanh toán linh hoạt.</p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="px-8 py-3.5 bg-white text-cloud-700 hover:bg-gray-50 text-base font-bold rounded-md shadow-lg transition-transform hover:-translate-y-0.5">
                Bắt đầu miễn phí
            </a>
        </div>
    </div>
</section>

@endsection
