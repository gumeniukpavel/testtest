<?php

namespace App\Jobs;

use App\Constant\ScheduleCalculationStatus;
use App\Db\Entity\ScheduleCalculation;
use App\Db\Entity\ScheduleCalculationCompany;
use App\Db\Service\ScheduleCalculationDao;
use App\Http\Requests\ScheduleCalculation\CreateScheduleCalculationRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ScheduleCalculationCallbackJob implements ShouldQueue
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

        /** @var ScheduleCalculation[] $scheduleCalculations */
        $scheduleCalculations = $scheduleCalculationDao->getPendingScheduleCalculations();

        foreach ($scheduleCalculations as $scheduleCalculation) {
            $companies = $scheduleCalculationDao->getCompletedScheduleCalculationCompanies($scheduleCalculation);

            if (count($companies) == 0) {
                $scheduleCalculation->status = ScheduleCalculationStatus::$EmptyResponse;
                $scheduleCalculation->save();
                continue;
            }

            /** @var CreateScheduleCalculationRequest $body */
            $body = json_decode($scheduleCalculation->data);

            /** @var ScheduleCalculationCompany[] | Collection $data */
            $data = $companies->pluck('response')->map(function ($response)
            {
                return json_decode($response, true);
            });
            dd($data);
            try {
                $response = Http::post($body->callbackUrl, $data->toArray());

                if ($response->status() == 200) {
                    $scheduleCalculation->status = ScheduleCalculationStatus::$Completed;
                    $scheduleCalculation->response = json_encode($response->json());
                    $scheduleCalculation->save();
                } else {
                    $scheduleCalculation->status = ScheduleCalculationStatus::$Failed;
                    $scheduleCalculation->error_message = json_encode($response->json());
                    $scheduleCalculation->save();
                }
            } catch (\Exception $exception) {
                Log::error($exception->getMessage());
                $scheduleCalculation->status = ScheduleCalculationStatus::$Failed;
                $scheduleCalculation->error_message = $exception->getMessage();
                $scheduleCalculation->save();
            }
        }
    }
}
