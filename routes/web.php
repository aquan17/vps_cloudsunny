<?php

use App\Http\Controllers\Admin\CloudSunnyAccountController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ProxyController as AdminProxyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\TopupController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Email Verification Routes
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [App\Http\Controllers\VerificationController::class, 'show'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [App\Http\Controllers\VerificationController::class, 'verify'])->middleware(['signed'])->name('verification.verify');
    Route::post('/email/verification-notification', [App\Http\Controllers\VerificationController::class, 'resend'])->middleware(['throttle:6,1'])->name('verification.resend');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/pricing', [StoreController::class, 'index'])->name('pricing');

Route::get('/fix-vps-37', function() {
    $api = app(App\Services\CloudSunnyApiService::class);
    $account = App\Models\CloudSunnyAccount::first();
    if ($account) {
        $api->forAccount($account);
    }
    
    $vpsList = $api->listVps();
    $target = null;
    $items = $vpsList['data'] ?? $vpsList;
    if (isset($items['items'])) {
        $items = $items['items'];
    }
    
    foreach ($items as $v) {
        $ip = $v['ip'] ?? $v['main_ip'] ?? $v['ip_address'] ?? '';
        if ($ip === '103.67.197.210') {
            $target = $v;
            break;
        }
    }
    
    if (!$target) {
        return "KHÔNG TÌM THẤY VPS NÀY TRÊN API (IP: 103.67.197.210). Hãy chắc chắn IP đúng.";
    }
    
    $vps = App\Models\VpsInstance::find(37);
    if (!$vps) {
        return "KHÔNG TÌM THẤY VPS CÓ ID 37 TRONG DATABASE CỦA BẠN.";
    }
    
    $providerId = $target['id'] ?? $target['vps_id'] ?? $target['server_id'] ?? null;
    $orderId = $target['order_id'] ?? $target['orderId'] ?? null;
    $loginUser = $target['username'] ?? $target['user'] ?? $target['login_username'] ?? 'Administrator';
    $rootPass = $target['password'] ?? $target['root_password'] ?? '6nTg39OT3A23skLv';
    
    $vps->provider_vps_id = $providerId;
    $vps->provider_order_id = $orderId;
    $vps->public_ip = '103.67.197.210';
    $vps->login_username = $loginUser;
    $vps->root_password = $rootPass; 
    $vps->status = 'Sẵn sàng';
    $vps->provider_payload = $target; 
    
    $vps->cpu = 32;
    $vps->ram = 32;
    $vps->disk = 200;
    
    $vps->save();
    
    return "<h2>ĐÃ ĐỒNG BỘ THÀNH CÔNG!</h2> Provider VPS ID: " . $providerId . "<br> Bạn có thể quay lại trang Quản lý VPS, sẽ thấy VPS ID 37 đã chuyển sang trạng thái Sẵn Sàng và có thể điều khiển bình thường.";
});

Route::get('/test-affiliate', function() {
    $referrer = \App\Models\User::whereHas('referrals')->first();
    if (!$referrer) {
        return "CHÚ Ý: Chưa có ai làm F1 cả. Hãy lấy link giới thiệu, mở tab ẩn danh và đăng ký 1 tài khoản mới trước để test nhé!";
    }
    
    $f1 = $referrer->referrals()->first();
    $oldBalance = $referrer->balance;
    
    // Giả lập F1 mua VPS giá 200,000 VND
    app(\App\Services\AffiliateService::class)->processCommission($f1, 200000, 'buy_vps');
    
    $referrer->refresh();
    $log = \App\Models\AffiliateLog::orderBy('id', 'desc')->first();
    
    return "<h2>TEST THÀNH CÔNG!</h2>"
         . "Tài khoản người giới thiệu: {$referrer->email}<br>"
         . "Số dư CŨ: {$oldBalance} VND<br>"
         . "Số dư MỚI: {$referrer->balance} VND<br><br>"
         . "=> F1 ({$f1->email}) vừa mua VPS 200k. Người giới thiệu nhận được {$log->commission} VND hoa hồng.";
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/affiliate', [App\Http\Controllers\AffiliateController::class, 'index'])->name('affiliate.index');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/vps/{vps}',          [DashboardController::class, 'show'])->name('dashboard.show');
    Route::get('/dashboard/vps/{vps}/status',   [DashboardController::class, 'statusJson'])->name('dashboard.status');
    Route::post('/dashboard/vps/{vps}/sync',    [DashboardController::class, 'sync'])->name('dashboard.sync');

    // Power management
    Route::post('/dashboard/vps/{vps}/reboot',   [DashboardController::class, 'reboot'])->name('dashboard.reboot');
    Route::post('/dashboard/vps/{vps}/shutdown', [DashboardController::class, 'shutdown'])->name('dashboard.shutdown');
    Route::post('/dashboard/vps/{vps}/boot',     [DashboardController::class, 'boot'])->name('dashboard.boot');
    Route::post('/dashboard/vps/{vps}/renew',    [DashboardController::class, 'renew'])->name('dashboard.renew');
    Route::post('/dashboard/vps/{vps}/upgrade',  [DashboardController::class, 'upgrade'])->name('dashboard.upgrade');

    // OS management
    Route::post('/dashboard/vps/{vps}/password', [DashboardController::class, 'changePassword'])->name('dashboard.password');
    Route::post('/dashboard/vps/{vps}/rebuild',  [DashboardController::class, 'rebuild'])->name('dashboard.rebuild');

    // Delete
    Route::delete('/dashboard/vps/{vps}',        [DashboardController::class, 'destroy'])->name('dashboard.destroy');

    // VPS Store routes
    Route::get('/store', [StoreController::class, 'index'])->name('store.index');
    Route::get('/store/{plan}', [StoreController::class, 'create'])->name('store.create');
    Route::post('/store', [StoreController::class, 'store'])->name('store.store');

    // Proxy Store routes
    Route::get('/proxy/store', [App\Http\Controllers\ProxyStoreController::class, 'index'])->name('proxy.store.index');
    Route::post('/proxy/store', [App\Http\Controllers\ProxyStoreController::class, 'store'])->name('proxy.store.store');

    // Proxy Management routes
    Route::get('/proxy', [App\Http\Controllers\ProxyController::class, 'index'])->name('proxy.index');
    Route::get('/proxy/{proxy}', [App\Http\Controllers\ProxyController::class, 'show'])->name('proxy.show');
    Route::get('/proxy/{proxy}/status', [App\Http\Controllers\ProxyController::class, 'statusJson'])->name('proxy.statusJson');
    Route::post('/proxy/{proxy}/renew', [App\Http\Controllers\ProxyController::class, 'renew'])->name('proxy.renew');
    Route::delete('/proxy/{proxy}', [App\Http\Controllers\ProxyController::class, 'destroy'])->name('proxy.destroy');

    // Top-ups
    Route::get('/topup',                [TopupController::class, 'index'])->name('topup.index');
    Route::post('/topup',               [TopupController::class, 'store'])->name('topup.store');
    Route::get('/topup/{id}',           [TopupController::class, 'show'])->name('topup.show');
    Route::get('/topup/{id}/status',    [TopupController::class, 'status'])->name('topup.status');

    // Profile
    Route::get('/profile',              [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile/password',     [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Billing (lịch sử thanh toán)
    Route::get('/billing',              [\App\Http\Controllers\BillingController::class, 'index'])->name('billing.index');

    // Support (Hỗ trợ)
    Route::view('/support', 'support.index')->name('support.index');
});

// Automated Payment Webhook
Route::post('/webhooks/topups', [TopupController::class, 'webhook'])->name('webhooks.topups');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // CloudSunny Accounts
    Route::get('/accounts',                    [CloudSunnyAccountController::class, 'index'])->name('accounts.index');
    Route::post('/accounts',                   [CloudSunnyAccountController::class, 'store'])->name('accounts.store');
    Route::get('/accounts/{account}/edit',     [CloudSunnyAccountController::class, 'edit'])->name('accounts.edit');
    Route::put('/accounts/{account}',          [CloudSunnyAccountController::class, 'update'])->name('accounts.update');
    Route::post('/accounts/sync-all',          [CloudSunnyAccountController::class, 'syncAll'])->name('accounts.sync-all');
    Route::post('/accounts/{account}/sync',    [CloudSunnyAccountController::class, 'sync'])->name('accounts.sync');
    Route::post('/accounts/{account}/toggle',  [CloudSunnyAccountController::class, 'toggle'])->name('accounts.toggle');
    Route::delete('/accounts/{account}',       [CloudSunnyAccountController::class, 'destroy'])->name('accounts.destroy');

    // Users
    Route::get('/users',                           [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}',                    [AdminUserController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/balance',           [AdminUserController::class, 'adjustBalance'])->name('users.balance');
    Route::post('/users/{user}/toggle-admin',      [AdminUserController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::delete('/users/{user}',                 [AdminUserController::class, 'destroy'])->name('users.destroy');

    // All Instances
    Route::get('/instances',                       [\App\Http\Controllers\Admin\InstanceController::class, 'index'])->name('instances.index');
    Route::get('/proxies',                         [AdminProxyController::class, 'index'])->name('proxies.index');
    Route::delete('/proxies/{proxy}',              [AdminProxyController::class, 'destroy'])->name('proxies.destroy');

    // Revenue
    Route::get('/revenue',                         [\App\Http\Controllers\Admin\RevenueController::class, 'index'])->name('revenue.index');
    Route::delete('/revenue/{transaction}',        [\App\Http\Controllers\Admin\RevenueController::class, 'destroy'])->name('revenue.destroy');
});
