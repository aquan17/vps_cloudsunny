<?php

use App\Http\Controllers\Admin\CloudSunnyAccountController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
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

Route::middleware(['auth', 'verified'])->group(function () {
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

    Route::get('/order/{plan}',  [StoreController::class, 'create'])->name('store.create');
    Route::post('/order',        [StoreController::class, 'store'])->name('store.store');

    // Top-ups
    Route::get('/topup',                [TopupController::class, 'index'])->name('topup.index');
    Route::post('/topup',               [TopupController::class, 'store'])->name('topup.store');
    Route::get('/topup/{id}',           [TopupController::class, 'show'])->name('topup.show');
    Route::get('/topup/{id}/status',    [TopupController::class, 'status'])->name('topup.status');

    // Profile
    Route::get('/profile',              [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile/password',     [ProfileController::class, 'updatePassword'])->name('profile.password');
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

    // Revenue
    Route::get('/revenue',                         [\App\Http\Controllers\Admin\RevenueController::class, 'index'])->name('revenue.index');
});
