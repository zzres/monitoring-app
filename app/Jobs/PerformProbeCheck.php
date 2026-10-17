<?php

namespace App\Jobs;

use App\Models\Probe;
use App\Services\ProbeCheckService;
use App\Notifications\ProbeStatusChanged;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
//use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class PerformProbeCheck implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uniqueFor = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(public Probe $probe)
    {
    }

    public function uniqueId(): string 
    {
        return (string) $this->probe->id;
    }

    /**
     * Execute the job.
     */
    public function handle(ProbeCheckService $checkService): void
    {
        $previousCheck = $this->probe->latestCheck;
        $wasUp = $previousCheck?->is_up;

        $result = $checkService->check($this->probe);

        $check = $this->probe->checks()->create([
            ...$result,
            'checked_at' => now(),
        ]);

        $statusChanged = $previousCheck !== null && $wasUp !== $check->is_up;

        if ($statusChanged) {
            $this->probe->user->notify(new ProbeStatusChanged($this->probe, $check));
        }
    }
}
