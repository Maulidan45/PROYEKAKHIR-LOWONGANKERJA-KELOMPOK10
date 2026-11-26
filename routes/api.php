<?php

use App\Http\Controllers\Api\V1\AutentikasiController;
use App\Http\Controllers\Api\V1\LowonganController;
use App\Http\Controllers\Api\V1\WilayahController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function (): void {
    Route::post('auth/login', [AutentikasiController::class, 'masuk'])->middleware('throttle:login')->name('auth.login');
    Route::get('wilayah/provinsi', [WilayahController::class, 'provinsi'])->name('wilayah.provinsi');
    Route::get('wilayah/kabupaten', [WilayahController::class, 'kabupaten'])->name('wilayah.kabupaten');
    Route::apiResource('lowongan', LowonganController::class)->only(['index', 'show']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('auth/logout', [AutentikasiController::class, 'keluar'])->name('auth.logout');
        Route::get('auth/me', [AutentikasiController::class, 'saya'])->name('auth.me');
        Route::apiResource('lowongan', LowonganController::class)->only(['store', 'update', 'destroy']);
    });
});
