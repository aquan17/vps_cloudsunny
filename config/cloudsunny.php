<?php

return [
    'api_base' => env('CLOUDSUNNY_API_BASE', 'https://api.cloudsunny.net/api'),
    'api_username' => env('CLOUDSUNNY_API_USERNAME'),
    'api_app' => env('CLOUDSUNNY_API_APP_KEY'),
    'api_secret' => env('CLOUDSUNNY_API_SECRET_KEY'),
    'cache_ttl' => (int) env('CLOUDSUNNY_CACHE_TTL', 300),

    'default_os_id' => (int) env('CLOUDSUNNY_DEFAULT_OS_ID', 1),
    'default_billing_cycle' => env('CLOUDSUNNY_DEFAULT_BILLING_CYCLE', 'monthly'),

    'addon_prices' => [
        'cpu_monthly' => (int) env('CLOUDSUNNY_ADDON_CPU_MONTHLY', 30000),
        'ram_monthly' => (int) env('CLOUDSUNNY_ADDON_RAM_MONTHLY', 12000),
        'disk_10gb_monthly' => (int) env('CLOUDSUNNY_ADDON_DISK_10GB_MONTHLY', 10000),
    ],

    // -----------------------------------------------------------------------
    // Chu kỳ thanh toán (Tháng, Quý, Năm) & Giảm giá
    // -----------------------------------------------------------------------
    'billing_cycles' => [
        'monthly' => ['months' => 1, 'label' => '1 Tháng', 'discount_percent' => 0],
        'quarterly' => ['months' => 3, 'label' => '3 Tháng', 'discount_percent' => 5],
        'semi_annually' => ['months' => 6, 'label' => '6 Tháng', 'discount_percent' => 10],
        'annually' => ['months' => 12, 'label' => '1 Năm', 'discount_percent' => 15],
        'biennially' => ['months' => 24, 'label' => '2 Năm', 'discount_percent' => 25],
        'triennially' => ['months' => 36, 'label' => '3 Năm', 'discount_percent' => 30],
    ],

    'plans' => [
        'nova-starter' => [
            'provider_id' => 'sn-1-1-20',
            'name' => 'Nova Starter',
            'desc' => 'Giải pháp tối ưu và tiết kiệm cho blog, tool và học tập.',
            'badge' => 'Tiết kiệm',
            'price_per_month' => 49999,
            'cores' => 1, 'ram' => 1, 'disk' => 20, 'transfer_tb' => 'Unlimited',
        ],
        'nova-basic' => [
            'provider_id' => 'sn-1-2-25',
            'name' => 'Nova Basic',
            'desc' => 'Phổ thông cho panel, bot và website vừa và nhỏ.',
            'badge' => null,
            'price_per_month' => 64900,
            'cores' => 1, 'ram' => 2, 'disk' => 25, 'transfer_tb' => 'Unlimited',
        ],
        'nova-standard' => [
            'provider_id' => 'sn-2-4-30',
            'name' => 'Nova Standard',
            'desc' => 'Hiệu năng ổn định cho các ứng dụng có lượng truy cập tầm trung.',
            'badge' => null,
            'price_per_month' => 119000,
            'cores' => 2, 'ram' => 4, 'disk' => 30, 'transfer_tb' => 'Unlimited',
        ],
        'nova-pro' => [
            'provider_id' => 'sn-4-8-50',
            'name' => 'Nova Pro',
            'desc' => 'Dành cho doanh nghiệp và website thương mại điện tử nhỏ.',
            'badge' => 'Phổ biến',
            'price_per_month' => 219000,
            'cores' => 4, 'ram' => 8, 'disk' => 50, 'transfer_tb' => 'Unlimited',
        ],
        'nova-premium' => [
            'provider_id' => 'sn-8-16-70',
            'name' => 'Nova Premium',
            'desc' => 'Sức mạnh vượt trội để xử lý cơ sở dữ liệu và app phức tạp.',
            'badge' => 'Cao cấp',
            'price_per_month' => 389000,
            'cores' => 8, 'ram' => 16, 'disk' => 70, 'transfer_tb' => 'Unlimited',
        ],
        'nova-vip' => [
            'provider_id' => 'sn-16-32-100',
            'name' => 'Nova V.I.P',
            'desc' => 'Dành cho dự án siêu khủng, chịu tải cao liên tục.',
            'badge' => 'V.I.P',
            'price_per_month' => 749000,
            'cores' => 16, 'ram' => 32, 'disk' => 100, 'transfer_tb' => 'Unlimited',
        ],
    ],

    'images' => [
        1 => ['label' => 'Windows Server 2012 R2', 'icon' => '🪟', 'group' => 'Windows'],
        2 => ['label' => 'Windows Server 2016', 'icon' => '🪟', 'group' => 'Windows'],
        3 => ['label' => 'Linux CentOS 7 64bit', 'icon' => '🐧', 'group' => 'Linux'],
        4 => ['label' => 'Windows Server 2019', 'icon' => '🪟', 'group' => 'Windows'],
        6 => ['label' => 'Linux Ubuntu', 'icon' => '🐧', 'group' => 'Linux'],
    ],
];
