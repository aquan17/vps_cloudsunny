<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$vps = \App\Models\VpsInstance::orderBy('id', 'desc')->first();
echo json_encode([
    'id' => $vps->id,
    'plan_id' => $vps->plan_id,
    'provider_product_id' => $vps->provider_product_id,
    'paid_amount' => $vps->paid_amount,
    'label' => $vps->label
], JSON_PRETTY_PRINT);
