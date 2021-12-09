<?php

namespace App\Db\Service;

use App\Constant\ScheduleCalculationCompanyStatus;
use App\Constant\ScheduleCalculationStatus;
use App\Db\Entity\CalculationCache;
use App\Db\Entity\ScheduleCalculation;
use App\Db\Entity\ScheduleCalculationCompany;
use App\Http\Requests\ScheduleCalculation\CreateScheduleCalculationRequest;
use App\Http\Service\CargoGuru\ApiService;
use App\Jobs\ScheduleCalculationJob;
use Carbon\Carbon;

class ScheduleCalculationDao
{
    private ApiService $apiService;
    private CompaniesCacheDao $companiesCacheDao;

    public function __construct(
        ApiService $apiService,
        CompaniesCacheDao $companiesCacheDao
    ) {
        $this->apiService = $apiService;
        $this->companiesCacheDao = $companiesCacheDao;
    }

    public function createScheduleCalculation(CreateScheduleCalculationRequest $request)
    {
        $body = $request->all();

        $scheduleCalculation = new ScheduleCalculation();
        $scheduleCalculation->data = json_encode($body);
        $scheduleCalculation->status = ScheduleCalculationStatus::$WaitingForCompanies;
        $scheduleCalculation->save();

        dispatch(new ScheduleCalculationJob());

        return $scheduleCalculation;
    }

    public function getWaitingForCompaniesScheduleCalculations()
    {
        return ScheduleCalculation::query()
            ->where('status', ScheduleCalculationStatus::$WaitingForCompanies->getValue())
            ->get();
    }

    public function getPendingScheduleCalculations()
    {
        return ScheduleCalculation::query()
            ->where('status', ScheduleCalculationStatus::$Pending->getValue())
            ->get();
    }

    public function getPendingScheduleCalculationCompanies(ScheduleCalculation $scheduleCalculation)
    {
        return ScheduleCalculationCompany::query()
            ->where([
                'status' => ScheduleCalculationCompanyStatus::$Pending->getValue(),
                'schedule_calculation_id' => $scheduleCalculation->id
            ])
            ->get();
    }

    public function getCompletedScheduleCalculationCompanies(ScheduleCalculation $scheduleCalculation)
    {
        return ScheduleCalculationCompany::query()
            ->where([
                'status' => ScheduleCalculationCompanyStatus::$Completed->getValue(),
                'schedule_calculation_id' => $scheduleCalculation->id
            ])
            ->get();
    }
}
