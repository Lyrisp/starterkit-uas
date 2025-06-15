<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BeritaController;

// Redirect root URL ke login
Route::get('/', function () {
    return redirect('/login');
});

// Authentication Routes
Auth::routes();

// Protected Routes - Harus login dulu
Route::middleware(['auth'])->group(function () {
    
    // Dashboard - Semua role bisa akses
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Routes untuk Berita
    Route::prefix('berita')->name('berita.')->group(function () {
        
        // Semua user yang login bisa lihat index berita
        Route::get('/', [BeritaController::class, 'index'])->name('index');
        
        // Create & Store - Hanya Admin dan Wartawan
        Route::middleware(['role:admin,wartawan'])->group(function () {
            Route::get('/create', [BeritaController::class, 'create'])->name('create');
            Route::post('/', [BeritaController::class, 'store'])->name('store');
        });
        
        // Show - Semua user bisa lihat detail
        Route::get('/{berita}', [BeritaController::class, 'show'])->name('show');
        
        // Edit & Update - Hanya Admin dan Wartawan (dengan validasi di controller)
        Route::middleware(['role:admin,wartawan'])->group(function () {
            Route::get('/{berita}/edit', [BeritaController::class, 'edit'])->name('edit');
            Route::put('/{berita}', [BeritaController::class, 'update'])->name('update');
        });
        
        // Delete - Hanya Admin dan Wartawan (dengan validasi di controller)
        Route::delete('/{berita}', [BeritaController::class, 'destroy'])
            ->name('destroy')
            ->middleware(['role:admin,wartawan']);
        
        // Approve & Reject - Hanya Admin dan Editor
        Route::middleware(['role:admin,editor'])->group(function () {
            Route::patch('/{berita}/approve', [BeritaController::class, 'approve'])->name('approve');
            Route::patch('/{berita}/reject', [BeritaController::class, 'reject'])->name('reject');
        });
    });
});