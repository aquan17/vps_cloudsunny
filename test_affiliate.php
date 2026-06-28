<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Services\AffiliateService;

// Tìm 1 người giới thiệu bất kỳ có F1
$referrer = User::whereHas('referrals')->first();

if (!$referrer) {
    echo "CHÚ Ý: Chưa có ai làm F1 cả. Hãy lấy link giới thiệu, mở tab ẩn danh và đăng ký 1 tài khoản mới trước để test nhé!\n";
    exit;
}

$f1 = $referrer->referrals()->first();
echo "Tài khoản người giới thiệu: {$referrer->email} (Balance: {$referrer->balance} VND)\n";
echo "Tài khoản F1: {$f1->email}\n";
echo "--- GIAO DỊCH GIẢ LẬP: F1 VỪA MUA VPS TRỊ GIÁ 200,000 VNĐ ---\n";

// Khởi chạy Service giả lập F1 mua VPS giá 200,000
$service = app(AffiliateService::class);
$service->processCommission($f1, 200000, 'buy_vps');

$referrer->refresh();
echo "=> Tiền hoa hồng đã được cộng!\n";
echo "Số dư MỚI của người giới thiệu: {$referrer->balance} VND\n";

$log = \App\Models\AffiliateLog::orderBy('id', 'desc')->first();
echo "=> Log vừa được tạo trong Database:\n";
echo "- Số tiền nhận: {$log->commission} VND\n";
echo "- Giao dịch: {$log->transaction_type}\n";
