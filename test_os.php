<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$api = app(\App\Services\CloudSunnyApiService::class);
$account = app(\App\Services\CloudSunnyAccountRouter::class)->firstActive();
$api->forAccount($account);

$res = $api->listOperatingSystems(1); // Assuming product_id 1
echo json_encode($res, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
