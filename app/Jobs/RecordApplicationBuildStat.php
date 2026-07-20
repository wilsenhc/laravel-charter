<?php

namespace App\Jobs;

use App\Models\ApplicationService;
use App\Models\ApplicationStat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class RecordApplicationBuildStat implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param  array<string, string|bool|null>  $data
     * @param  list<string>  $services
     */
    public function __construct(
        public array $data,
        public array $services,
    ) {}

    public function handle(): void
    {
        $data = $this->data;
        ksort($data);

        $services = $this->services;
        sort($services);

        $fingerprint = hash('sha256', serialize([
            ...$data,
            'services' => $services,
        ]));

        if (! Cache::add("stat_dedup:{$fingerprint}", true, 600)) {
            return;
        }

        $stat = ApplicationStat::create($this->data);

        if ($this->services !== ['none']) {
            $stat->services()->sync(
                ApplicationService::whereIn('name', $this->services)->pluck('id'),
            );
        }
    }
}
