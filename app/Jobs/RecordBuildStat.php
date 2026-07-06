<?php

namespace App\Jobs;

use App\Models\Service;
use App\Models\Stat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class RecordBuildStat implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param  array<string, string|bool|null>  $data
     * @param  list<string>  $services
     */
    public function __construct(
        public array $data,
        public array $services,
        public string $userAgent,
    ) {}

    public function handle(): void
    {
        if (Str::contains($this->userAgent, 'Mozilla')) {
            return;
        }

        $fingerprint = hash('sha256', serialize([
            ...$this->data,
            'services' => $this->services,
        ]));

        if (! Cache::add("stat_dedup:{$fingerprint}", true, 600)) {
            return;
        }

        $stat = Stat::create($this->data);

        if ($this->services !== ['none']) {
            $stat->services()->sync(
                Service::whereIn('name', $this->services)->pluck('id'),
            );
        }
    }
}
