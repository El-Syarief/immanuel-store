<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('items/export/excel', [ItemController::class, 'exportExcel'])->name('items.export.excel');
    Route::get('items/export/pdf', [ItemController::class, 'exportPdf'])->name('items.export.pdf');
    Route::resource('items', ItemController::class);

    Route::get('transactions/export/excel', [TransactionController::class, 'exportExcel'])->name('transactions.export.excel');
    Route::get('transactions/export/pdf', [TransactionController::class, 'exportPdf'])->name('transactions.export.pdf');
    Route::resource('transactions', TransactionController::class);
    // Route Laporan Keuangan
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
    Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');

    Route::get('/histories', [HistoryController::class, 'index'])->name('histories.index');

    Route::resource('users', UserController::class);
});

require __DIR__.'/auth.php';
