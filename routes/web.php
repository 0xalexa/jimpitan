<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WargaController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\ScanController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin Routes
    Route::middleware(['role:admin'])->group(function () {
        // ... Admin specific routes ...
    });

    // Sekretaris Routes
    Route::middleware(['role:sekretaris,admin'])->group(function () {
        Route::resource('warga', WargaController::class);
        
        Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
        Route::get('/transaksi/export', [TransaksiController::class, 'export'])->name('transaksi.export');
        Route::get('/transaksi/export-pdf', [TransaksiController::class, 'exportPdf'])->name('transaksi.export.pdf');
        Route::post('/transaksi/topup', [TransaksiController::class, 'topup'])->name('transaksi.topup');
        Route::post('/transaksi/manual', [TransaksiController::class, 'manualPayment'])->name('transaksi.manual');
        Route::post('/transaksi/pengeluaran', [TransaksiController::class, 'storeExpenditure'])->name('transaksi.pengeluaran');
        Route::post('/transaksi/donasi', [TransaksiController::class, 'storeDonation'])->name('transaksi.donasi');
        Route::delete('/transaksi/{id}', [TransaksiController::class, 'destroy'])->name('transaksi.destroy');
    });

    // Petugas Lapangan Routes (HANYA PETUGAS - Admin tidak boleh scan)
    Route::middleware(['role:petugas'])->group(function () {
        Route::get('/scan', [ScanController::class, 'index'])->name('scan.index');
        Route::post('/scan/process', [ScanController::class, 'process'])->name('scan.process');
    });
});
