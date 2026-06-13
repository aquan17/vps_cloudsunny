<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$api = app(\App\Services\CloudSunnyApiService::class);
$account = app(\App\Services\CloudSunnyAccountRouter::class)->firstActive();

if ($account) {
    $products = $api->forAccount($account)->listProducts();
    echo json_encode([
        'proxy_products' => $products['proxy'] ?? [],
        'proxy_categories' => $products['proxy_categories'] ?? []
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
    echo "No active account.";
}
