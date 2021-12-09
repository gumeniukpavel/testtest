<?php

namespace App\Http\Requests\ScheduleCalculation;

use App\Db\Entity\ScheduleCalculation;
use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

/**
 * GetScheduleCalculationStatus
 *
 * @property integer $scheduleCalculationId
 */
class GetScheduleCalculationStatus extends ApiFormRequest
{
    public function rules()
    {
        return [
            'scheduleCalculationId' => [
                'required',
                Rule::exists(ScheduleCalculation::class, 'id')
            ],
        ];
    }
}
