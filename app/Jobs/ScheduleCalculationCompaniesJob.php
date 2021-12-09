<?php

namespace App\Jobs;

use App\Db\Entity\ScheduleCalculation;
use App\Db\Service\ScheduleCalculationDao;
use App\Http\Service\CargoGuru\ApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScheduleCalculationCompaniesJob implements ShouldQueue
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
        /** @var ApiService $apiService */
        $apiService = app()->get(ApiService::class);

        /** @var ScheduleCalculation[] $scheduleCalculations */
        $scheduleCalculations = $scheduleCalculationDao->getPendingScheduleCalculations();

        foreach ($scheduleCalculations as $scheduleCalculation) {
            $companies = $scheduleCalculationDao->getPendingScheduleCalculationCompanies($scheduleCalculation);

            foreach ($companies as $company) {
                $apiService->getScheduleCalculation($scheduleCalculation, $company);
            }

            dispatch(new ScheduleCalculationCallbackJob());
        }
    }
}
