<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\CloudSunnyAccount;
use Illuminate\Support\Facades\Http;

$account = CloudSunnyAccount::first();
if (!$account) {
    echo "Không tìm thấy tài khoản API trong database.\n";
    exit;
}

echo "Đang thử kết nối với tài khoản: " . $account->api_username . "\n\n";

try {
    echo "1. Thử lấy token mới từ SeaServer (get-access-token)...\n";
    $response = Http::post('https://api.cloudsunny.net/api/agency/get-access-token', [
        'api_username' => $account->api_username,
        'api_app' => $account->api_app,
        'api_secret' => $account->api_secret,
    ]);
    
    echo "HTTP Status: " . $response->status() . "\n";
    echo "Raw Body: " . $response->body() . "\n\n";

    if ($response->successful() && isset($response->json()['data']['access_token'])) {
        $token = $response->json()['data']['access_token'];
        echo "2. Dùng token mới gọi danh-sach-san-pham...\n";
        $res2 = Http::withToken($token)
            ->acceptJson()
            ->get('https://api.cloudsunny.net/api/agency/danh-sach-san-pham');
            
        echo "HTTP Status: " . $res2->status() . "\n";
        echo "Raw Body: " . substr($res2->body(), 0, 500) . "\n";
    }

} catch (\Throwable $e) {
    echo "=> LỖI: " . $e->getMessage() . "\n";
}
