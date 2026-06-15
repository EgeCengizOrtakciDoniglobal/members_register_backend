<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\IntegrationController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public
Route::post('/login', [AuthController::class, 'login']);

// Protected (Sanctum bearer token required)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('members', MemberController::class);

    // Kullanıcı yönetimi — yalnızca yönetici (admin) rolündeki kullanıcılar erişebilir.
    Route::middleware('admin')->apiResource('users', UserController::class);
});

// WordPress entegrasyon ucu — statik API Key ile korunur (X-API-Key header).
// Yalnızca aktif üyeleri döner.
Route::middleware('apikey')->prefix('v1/integration')->group(function () {
    Route::get('/members', [IntegrationController::class, 'members']);
});
