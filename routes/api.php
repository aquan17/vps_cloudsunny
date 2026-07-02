<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Reseller API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('v1')->middleware(\App\Http\Middleware\ResellerApiAuth::class)->group(function () {
    Route::get('/profile', [\App\Http\Controllers\Api\Reseller\ResellerEndpointController::class, 'profile']);
    Route::get('/plans', [\App\Http\Controllers\Api\Reseller\ResellerEndpointController::class, 'plans']);
    Route::post('/vps/create', [\App\Http\Controllers\Api\Reseller\ResellerEndpointController::class, 'createVps']);
    Route::get('/vps/{order_id}/status', [\App\Http\Controllers\Api\Reseller\ResellerEndpointController::class, 'vpsStatus']);
});
