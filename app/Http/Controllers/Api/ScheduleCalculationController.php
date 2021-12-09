<?php

namespace App\Http\Controllers\Api;

use App\Db\Entity\ScheduleCalculation;
use App\Db\Service\CalculationCacheDao;
use App\Db\Service\ScheduleCalculationDao;
use App\Db\Service\UserRequestHistoryDao;
use App\Http\Controllers\BaseController;
use App\Http\Requests\CalculationCache\GetCalculationRequest;
use App\Http\Requests\ScheduleCalculation\CreateScheduleCalculationRequest;
use App\Http\Requests\ScheduleCalculation\GetScheduleCalculationStatus;
use App\Http\Service\CargoGuru\ApiService;
use App\Service\AuthService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ScheduleCalculationController extends BaseController
{
    private ScheduleCalculationDao $scheduleCalculationDao;
    private UserRequestHistoryDao $userRequestHistoryDao;

    public function __construct(
        AuthService $authService,
        ScheduleCalculationDao $scheduleCalculationDao,
        UserRequestHistoryDao $userRequestHistoryDao
    ) {
        parent::__construct($authService);
        $this->scheduleCalculationDao = $scheduleCalculationDao;
        $this->userRequestHistoryDao = $userRequestHistoryDao;
    }

    public function actionScheduleCalculation(CreateScheduleCalculationRequest $request)
    {
        $this->userRequestHistoryDao->createUserRequest(
            $request->all(),
            $request->getRequestUri(),
            $this->authService->getUser()
        );

        $scheduleCalculation = $this->scheduleCalculationDao->createScheduleCalculation($request);

        return $this->json($scheduleCalculation);
    }

    public function actionGetStatus(GetScheduleCalculationStatus $request)
    {
        $this->userRequestHistoryDao->createUserRequest(
            $request->all(),
            $request->getRequestUri(),
            $this->authService->getUser()
        );

        $scheduleCalculation = ScheduleCalculation::byId($request->scheduleCalculationId);

        return $this->json($scheduleCalculation);
    }

    public function actionLogCallback(Request $request)
    {
        Log::info(json_encode($request->all()));

        return $this->json();
    }
}
