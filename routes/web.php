<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\MonitorController;
use App\Http\Controllers\HistoryController;
use Illuminate\Support\Facades\Route;

// ─── Auth ─────────────────────────────────────────────────────────────────────
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ─── Protected ────────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Pengambilan Antrian (Mahasiswa)
    Route::get('/antrian/ambil', [QueueController::class, 'take'])->name('queue.take');
    Route::post('/antrian/ambil', [QueueController::class, 'store'])->name('queue.store');

    // Kelola Antrian (Staff)
    Route::get('/antrian/kelola', [QueueController::class, 'manage'])->name('queue.manage');
    Route::post('/antrian/{id}/panggil', [QueueController::class, 'call'])->name('queue.call');
    Route::post('/antrian/{id}/mulai', [QueueController::class, 'serve'])->name('queue.serve');
    Route::post('/antrian/{id}/selesai', [QueueController::class, 'done'])->name('queue.done');
    Route::post('/antrian/{id}/skip', [QueueController::class, 'skip'])->name('queue.skip');
    Route::post('/antrian/panggil-berikutnya', [QueueController::class, 'callNext'])->name('queue.callNext');

    // Monitor
    Route::get('/monitor', [MonitorController::class, 'index'])->name('monitor.index');

    // History
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
});
