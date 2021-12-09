<?php

namespace App\Db\Entity;

use App\Constant\ScheduleCalculationCompanyStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ScheduleCalculationCompany
 * @package App\Db\Entity
 *
 * @property integer $id
 * @property integer $schedule_calculation_id
 * @property integer $companies_cache_id
 * @property string $response
 *
 * @property ScheduleCalculationCompanyStatus $status
 *
 * @property CompaniesCache $company
 * @property ScheduleCalculation $scheduleCalculation
 */
class ScheduleCalculationCompany extends BaseEntity
{
    protected $visible = [
        'id',
        'status',
        'response',
    ];

    protected $fillable = [
        'status',
        'data',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(CompaniesCache::class, 'companies_cache_id');
    }

    public function scheduleCalculation(): BelongsTo
    {
        return $this->belongsTo(ScheduleCalculation::class);
    }

    public function getStatusAttribute()
    {
        return ScheduleCalculationCompanyStatus::getEnumObject($this->attributes['status']);
    }

    public function setStatusAttribute(ScheduleCalculationCompanyStatus $status)
    {
        $this->attributes['status'] = $status->getValue();
    }
}
