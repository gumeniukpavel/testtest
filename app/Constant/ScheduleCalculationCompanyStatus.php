<?php

namespace App\Constant;

use App\Constant\Common\Enum;

class ScheduleCalculationCompanyStatus extends Enum
{
    public static ScheduleCalculationCompanyStatus $Pending;
    public static ScheduleCalculationCompanyStatus $Completed;
    public static ScheduleCalculationCompanyStatus $Failed;
}

ScheduleCalculationCompanyStatus::$Pending = new ScheduleCalculationCompanyStatus('Pending', 'Pending');
ScheduleCalculationCompanyStatus::$Completed = new ScheduleCalculationCompanyStatus('Completed', 'Completed');
ScheduleCalculationCompanyStatus::$Failed = new ScheduleCalculationCompanyStatus('Failed', 'Failed');

ScheduleCalculationCompanyStatus::init();
