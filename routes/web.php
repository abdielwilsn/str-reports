<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('reports/{user}', [ReportsController::class, 'show'])->name('reports.show');
});

require __DIR__.'/settings.php';
