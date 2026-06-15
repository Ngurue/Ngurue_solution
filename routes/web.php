<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecordController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Models\Record;
use Carbon\Carbon;

use Inertia\Inertia;

Route::get('/test-php', function () {
    return '<h1>PHP engine is working perfectly!</h1>';
});

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    // 1. Takwimu Kuu (Kadi)
    $stats = [
        'total_pigs' => Record::where('status', '!=', 'Aliekufa')->count(),
        'boars'      => Record::where('gender', 'Dume')->where('record_type', 'pig')->count(),
        'sows'       => Record::where('gender', 'Jike')->where('record_type', 'pig')->count(),
        'weaners'    => Record::where('status', 'Mtoto')->orWhere('record_type', 'litter')->count(),
    ];

    // 2. Data ya Grafu ya Breeds (Group By Breed) - MPYA
    // Hii itarudisha list ya breed na idadi yao, mfano: [['breed' => 'Large White', 'total' => 12], ...]
    $breedData = Record::select('breed', DB::raw('count(*) as total'))
        ->where('status', '!=', 'Aliekufa')
        ->whereNotNull('breed')
        ->groupBy('breed')
        ->get();

    $breedLabels = $breedData->pluck('breed')->toArray();
    $breedCounts = $breedData->pluck('total')->toArray();

    // 3. Rekodi za hivi karibuni
    $recent_activities = Record::orderBy('created_at', 'desc')
        ->take(5)
        ->get(['pig_code', 'record_type', 'breed', 'created_at']);

    return Inertia::render('Dashboard', [
        'stats' => $stats,
        'breedChartData' => [
            'labels' => !empty($breedLabels) ? $breedLabels : ['Sio rasmi'],
            'dataset' => !empty($breedCounts) ? $breedCounts : [0]
        ],
        'recentActivities' => $recent_activities
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

// HAPA NDIPO PENYE ULINZI: Njia zote za Nguruwe zinalindwa pamoja hapa
Route::middleware(['auth', 'verified'])->group(function () {
    // Route ya Uzito lazima iwe juu ya Resource ili isivurugwe, na imelindwa hapa ndani sasa!
    Route::put('records/{record}/weight', [RecordController::class, 'updateWeight'])->name('records.updateWeight');
    Route::resource('records', RecordController::class);
    
});

Route::middleware('auth')->group(function () {
    // Njia za kuhariri taarifa za mtumiaji (Profile)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/records/{id}', [RecordController::class, 'destroy'])->name('records.destroy');
});

require __DIR__.'/auth.php';