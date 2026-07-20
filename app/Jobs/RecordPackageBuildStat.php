<?php

namespace App\Jobs;

use App\Models\PackageStat;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;

class RecordPackageBuildStat implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array<string, string|bool>  $data
     */
    public function __construct(
        public array $data,
    ) {}

    public function handle(): void
    {
        $data = $this->data;
        ksort($data);

        $fingerprint = hash('sha256', serialize($data));

        if (! Cache::add("package_stat_dedup:{$fingerprint}", true, 600)) {
            return;
        }

        PackageStat::create($data);
    }
}
