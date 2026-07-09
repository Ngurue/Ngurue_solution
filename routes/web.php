<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecordController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Njia zote za rekodi za nguruwe zimelindwa kwa pamoja hapa
Route::middleware(['auth', 'verified'])->group(function () {
    // Njia ya uzito lazima iwe juu ya resource ili isivurugwe
    Route::put('records/{record}/weight', [RecordController::class, 'updateWeight'])->name('records.updateWeight');
    Route::resource('records', RecordController::class)->except(['create', 'show', 'edit']);
});

// Njia za kuhariri taarifa za mtumiaji (Profile)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
