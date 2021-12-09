<?php

namespace App\Jobs;

use App\Constant\ScheduleCalculationCompanyStatus;
use App\Constant\ScheduleCalculationStatus;
use App\Db\Entity\ScheduleCalculation;
use App\Db\Entity\ScheduleCalculationCompany;
use App\Db\Service\CompaniesCacheDao;
use App\Db\Service\ScheduleCalculationDao;
use App\Http\Service\CargoGuru\ApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScheduleCalculationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /** @var ScheduleCalculationDao $scheduleCalculationDao */
        $scheduleCalculationDao = app()->get(ScheduleCalculationDao::class);
        /** @var CompaniesCacheDao $companiesCacheDao */
        $companiesCacheDao = app()->get(CompaniesCacheDao::class);
        /** @var ApiService $apiService */
        $apiService = app()->get(ApiService::class);

        /** @var ScheduleCalculation[] $scheduleCalculations */
        $scheduleCalculations = $scheduleCalculationDao->getWaitingForCompaniesScheduleCalculations();

        foreach ($scheduleCalculations as $scheduleCalculation) {
            $actualCompanies = $apiService->getActualCompanies($scheduleCalculation);

            foreach ($actualCompanies as $actualCompany) {
                $company = $companiesCacheDao->createCompaniesCache($actualCompany);

                $scheduleCalculationCompany = new ScheduleCalculationCompany();
                $scheduleCalculationCompany->schedule_calculation_id = $scheduleCalculation->id;
                $scheduleCalculationCompany->companies_cache_id = $company->id;
                $scheduleCalculationCompany->status = ScheduleCalculationCompanyStatus::$Pending;
                $scheduleCalculationCompany->save();
            }

            $scheduleCalculation->status = ScheduleCalculationStatus::$Pending;
            $scheduleCalculation->save();

            dispatch(new ScheduleCalculationCompaniesJob());
        }
    }
}
