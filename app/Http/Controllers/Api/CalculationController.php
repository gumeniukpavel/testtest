<?php

namespace App\Http\Controllers\Api;

use App\Db\Service\CalculationCacheDao;
use App\Db\Service\UserRequestHistoryDao;
use App\Http\Controllers\BaseController;
use App\Http\Requests\CalculationCache\GetCalculationRequest;
use App\Http\Service\CargoGuru\ApiService;
use App\Service\AuthService;

class CalculationController extends BaseController
{
    private CalculationCacheDao $calculationCacheDao;
    private ApiService $apiService;
    private UserRequestHistoryDao $userRequestHistoryDao;

    public function __construct(
        AuthService $authService,
        CalculationCacheDao $calculationCacheDao,
        ApiService $apiService,
        UserRequestHistoryDao $userRequestHistoryDao
    ) {
        parent::__construct($authService);
        $this->calculationCacheDao = $calculationCacheDao;
        $this->apiService = $apiService;
        $this->userRequestHistoryDao = $userRequestHistoryDao;
    }

    public function actionGetCalculation(GetCalculationRequest $request)
    {
        $this->userRequestHistoryDao->createUserRequest(
            $request->all(),
            $request->getRequestUri(),
            $this->authService->getUser()
        );
        $calculation = $this->apiService->getCalculation($request);

        if ($calculation) {
            return $this->json($calculation->response);
        } else {
            return $this->json($calculation);
        }
    }
}
