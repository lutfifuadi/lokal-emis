<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\SekolahApiController;
use App\Http\Controllers\api\JurusanApiController;
use App\Http\Controllers\api\TahunAjaranApiController;
use App\Http\Controllers\api\KelasApiController;
use App\Http\Controllers\api\UserApiController;
use App\Http\Controllers\api\SiswaApiController;
use App\Http\Controllers\api\SelfServiceApiController;
use App\Http\Controllers\api\ApprovalApiController;

// Public auth endpoints
Route::post('/login', [AuthController::class, 'login']);

// Authenticated endpoints
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);

    // Master Data CRUD (Super Admin and Operator only, using web guard for roles)
    Route::middleware('role:Super Admin|Operator')->group(function () {
        Route::apiResource('sekolah', SekolahApiController::class);
        Route::apiResource('jurusan', JurusanApiController::class);
        Route::apiResource('tahun-ajaran', TahunAjaranApiController::class);
        Route::apiResource('kelas', KelasApiController::class);
        Route::apiResource('users', UserApiController::class);
        Route::apiResource('siswa', SiswaApiController::class);
    });

    // Self-Service (Siswa, Orang Tua, and Guru, using web guard for roles)
    Route::middleware('role:Siswa|Orang Tua|Guru')->prefix('self-service')->group(function () {
        Route::get('/profil', [SelfServiceApiController::class, 'profil']);
        Route::post('/perubahan', [SelfServiceApiController::class, 'submitPerubahan']);
        Route::get('/perubahan/history', [SelfServiceApiController::class, 'perubahanHistory']);
    });

    // Approval Queue (Super Admin, Operator, and Kepala Sekolah, using web guard for roles)
    // Approval Queue (Super Admin, Operator, and Kepala Sekolah, using web guard for roles)
    Route::middleware('role:Super Admin|Operator|Kepala Sekolah')->prefix('approval')->group(function () {
        Route::get('/antrian', [ApprovalApiController::class, 'antrian']);
        Route::post('/approve/{id}', [ApprovalApiController::class, 'approve']);
        Route::post('/reject/{id}', [ApprovalApiController::class, 'reject']);
    });
});
