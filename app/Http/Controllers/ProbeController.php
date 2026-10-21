<?php

namespace App\Http\Controllers;

use App\Models\Probe;
use App\Jobs\PerformProbeCheck;
use App\Rules\PublicTarget;
use Illuminate\Http\Request;

class ProbeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $probes = auth()->user()->probes()
            ->with('latestCheck')
            ->latest()
            ->get();
        
        return view('probes.index', compact('probes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('probes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Probe::count() >= 30) {
            return back()->withErrors(['name' => 'La démo a atteint sa capacité maximale de sondes.'])->withInput();
        } 

        if (auth()->user()->probes()->count() >= 10) {
            return back()->withErrors(['name' => 'Limite de 10 sondes atteinte pour cette démo.'])->withInput();
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:http,tcp',
            'target' => ['required', 'string', 'max:255', new PublicTarget((string) $request->input('type'))],
            'check_interval_minutes' => 'required|integer|min:5|max:1440',
        ]);

        auth()->user()->probes()->create($validated);

        return redirect()->route('probes.index')->with('success', 'Sonde créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Probe $probe)
    {
        $this->authorizeProbe($probe);

        $checks = $probe->checks()->latest('checked_at')->take(50)->get();

        $uptimePercentage = $this->calculateUptime($probe);

        return view('probes.show', compact('probe', 'checks', 'uptimePercentage'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Probe $probe)
    {
        $this->authorizeProbe($probe);

        return view('probes.edit', compact('probe'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Probe $probe)
    {
        $this->authorizeProbe($probe);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:http,tcp',
            'target' => ['required', 'string', 'max:255', new PublicTarget((string) $request->input('type'))],
            'check_interval_minutes' => 'required|integer|min:5|max:1440',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $probe->update($validated);

        return redirect()->route('probes.index')->with('success', 'Sonde mise à jour.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Probe $probe)
    {
        $this->authorizeProbe($probe);

        $probe->delete();

        return redirect()->route('probes.index')->with('success', 'Sonde supprimée.');
    }

    private function authorizeProbe(Probe $probe): void 
    {
        if ($probe->user_id !== auth()->id()) {
            abort(403);
        }
    }

    private function calculateUptime(Probe $probe): float 
    {
        $total = $probe->checks()->count();

        if ($total === 0) {
            return 100.0;
        }

        $up = $probe->checks()->where('is_up', true)->count();

        return round(($up / $total) * 100, 1);
    }

    public function checkNow(Probe $probe)
    {
        $this->authorizeProbe($probe);

        PerformProbeCheck::dispatchSync($probe);

        return back()->with('success', 'Vérification effectuée.');
    }
}
