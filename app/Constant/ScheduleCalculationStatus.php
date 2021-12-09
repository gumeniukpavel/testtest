<?php

namespace App\Constant;

use App\Constant\Common\Enum;

class ScheduleCalculationStatus extends Enum
{
    public static ScheduleCalculationStatus $Pending;
    public static ScheduleCalculationStatus $WaitingForCompanies;
    public static ScheduleCalculationStatus $Completed;
    public static ScheduleCalculationStatus $Failed;
    public static ScheduleCalculationStatus $EmptyResponse;
}

ScheduleCalculationStatus::$Pending = new ScheduleCalculationStatus('Pending', 'Pending');
ScheduleCalculationStatus::$WaitingForCompanies = new ScheduleCalculationStatus('WaitingForCompanies',
    'WaitingForCompanies');
ScheduleCalculationStatus::$Completed = new ScheduleCalculationStatus('Completed', 'Completed');
ScheduleCalculationStatus::$Failed = new ScheduleCalculationStatus('Failed', 'Failed');
ScheduleCalculationStatus::$EmptyResponse = new ScheduleCalculationStatus('EmptyResponse', 'EmptyResponse');

ScheduleCalculationStatus::init();
