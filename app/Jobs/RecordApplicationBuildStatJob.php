<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\ApplicationService;
use App\Models\ApplicationStat;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;

class RecordApplicationBuildStatJob implements ShouldQueue
{
    use Queueable;

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
