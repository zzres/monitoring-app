<?php

namespace App\Console\Commands;

use App\Jobs\PerformProbeCheck;
use App\Models\Probe;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class RunDueProbe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     
     * protected $signature = 'app:run-due-probe';
     */
    protected $signature = 'probes:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Déclenche la vérification de toutes les sondes actives dont l\'intervalle est écoulé';

    /**
     * Execute the console command.
     */
    public function handle(): int 
    {
        $probes = Probe::where('is_active', true)
            ->with('latestCheck')
            ->get();

        $dispatched = 0;
        $errors = [];

        foreach ($probes as $probe) {
            if ($probe->isDue()) {
                continue;
            }

            try {
                PerformProbeCheck::dispatch($probe);
                $dispatched++;
            } catch (\Throwable $e) {
                $errors[] = "{$probe->name} : {$e->getMessage()}";
            }
        }

        Cache::put('scheduler:last_run', [
            'at' => now()->toIso8601String(),
            'total' => $probes->count(),
            'dispatched' => $dispatched,
            'errors' => $errors,
        ], now()->addDay());

        $this->info("{$dispatched} vérification(s) planifiée(s).");

        return self::SUCCESS;
    }
}
