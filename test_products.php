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
} else {
    echo "No active account.";
}
