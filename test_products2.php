<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$api = app(\App\Services\CloudSunnyApiService::class);
$account = app(\App\Services\CloudSunnyAccountRouter::class)->firstActive();

if ($account) {
    $products = $api->forAccount($account)->listProducts();
    echo json_encode(array_keys($products), JSON_PRETTY_PRINT);
    echo "\n\n";
    // Search for proxy in any key
    foreach ($products as $key => $val) {
        if (strpos($key, 'proxy') !== false) {
            echo "Found $key:\n";
            echo json_encode($val, JSON_PRETTY_PRINT);
            echo "\n";
        }
    }
} else {
    echo "No active account.";
}
