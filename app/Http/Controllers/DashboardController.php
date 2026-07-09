<?php

namespace App\Http\Controllers;

use App\Models\Record;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $base = fn () => Record::query()->visibleTo($user);

        // 1. Takwimu kuu (Kadi) — hesabu wanyama hai pekee kwenye makundi
        $stats = [
            'total_pigs' => $base()->where('status', '!=', 'Aliekufa')->count(),
            'boars' => $base()->where('gender', 'Dume')->where('record_type', 'pig')->where('status', '!=', 'Aliekufa')->count(),
            'sows' => $base()->where('gender', 'Jike')->where('record_type', 'pig')->where('status', '!=', 'Aliekufa')->count(),
            'weaners' => $base()->where('status', '!=', 'Aliekufa')->where(function ($q) {
                $q->where('status', 'Mtoto')->orWhere('record_type', 'litter');
            })->count(),
            'deceased' => $base()->where('status', 'Aliekufa')->count(),
        ];

        // 2. Mchanganuo wa breeds kwa grafu
        $breedData = $base()
            ->select('breed', DB::raw('count(*) as total'))
            ->where('status', '!=', 'Aliekufa')
            ->whereNotNull('breed')
            ->groupBy('breed')
            ->orderByDesc('total')
            ->get();

        $breedLabels = $breedData->pluck('breed')->toArray();
        $breedCounts = $breedData->pluck('total')->toArray();

        // 3. Rekodi za hivi karibuni
        $recentActivities = $base()
            ->latest()
            ->take(5)
            ->get(['pig_code', 'record_type', 'breed', 'created_at']);

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'breedChartData' => [
                'labels' => ! empty($breedLabels) ? $breedLabels : ['Sio rasmi'],
                'dataset' => ! empty($breedCounts) ? $breedCounts : [0],
            ],
            'recentActivities' => $recentActivities,
        ]);
    }
}
