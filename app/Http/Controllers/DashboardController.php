<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $probes = auth()->user()->probes()
            ->with('latestCheck')
            ->get();

        $total = $probes->count();
        $up = $probes->filter(fn($probe) => $probe->latestCheck?->is_up === true)->count();
        $down = $probes->filter(fn($probe) => $probe->latestCheck?->is_up === false)->count();

        $downProbes = $probes->filter(fn($probe) => $probe->latestCheck?->is_up === false);

        return view('dashboard', [
            'total' => $total,
            'up' => $up,
            'down' => $down,
            'downProbes' => $downProbes,
            'probes' => $probes->sortByDesc(fn($probe) => $probe->latestCheck?->checked_at),
            'schedulerStatus' => Cache::get('scheduler:last_run'),
        ]);
    }
}
