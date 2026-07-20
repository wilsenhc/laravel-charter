<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperApplicationService
 */
class ApplicationService extends Model
{
    protected $table = 'application_services';

    /**
     * @return BelongsToMany<ApplicationStat, $this>
     */
    public function stats(): BelongsToMany
    {
        return $this->belongsToMany(ApplicationStat::class, 'application_stat_services', 'service_id', 'stat_id');
    }
}
