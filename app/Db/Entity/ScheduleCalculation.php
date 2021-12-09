<?php

namespace App\Db\Entity;

use App\Constant\ScheduleCalculationStatus;

/**
 * Class ScheduleCalculation
 * @package App\Db\Entity
 *
 * @property integer $id
 * @property string $data
 * @property string $response
 * @property string $error_message
 *
 * @property ScheduleCalculationStatus $status
 */
class ScheduleCalculation extends BaseEntity
{
    protected $visible = [
        'id',
        'data',
        'status',
        'response',
        'error_message'
    ];

    protected $fillable = [
        'data',
        'status',
        'data',
    ];

    public function setStatusAttribute(ScheduleCalculationStatus $status)
    {
        $this->attributes['status'] = $status->getValue();
    }
}
