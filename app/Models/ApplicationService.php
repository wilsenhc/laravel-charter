<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperApplicationService
 */
#[Table(name: 'application_services')]
class ApplicationService extends Model
{
    /**
     * @return BelongsToMany<ApplicationStat, $this>
     */
    public function stats(): BelongsToMany
    {
        return $this->belongsToMany(ApplicationStat::class, 'application_stat_services', 'service_id', 'stat_id');
    }
}
