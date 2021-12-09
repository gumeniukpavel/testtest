<?php

namespace App\Http\Controllers\Api;

use App\Db\Entity\CompaniesCache;
use App\Db\Service\UserRequestHistoryDao;
use App\Http\Controllers\BaseController;
use App\Http\Requests\GetCompanyOrderOptionsRequest;
use App\Http\Service\CargoGuru\ApiService;
use App\Service\AuthService;

class CompanyPaymentController extends BaseController
{
    private ApiService $apiService;
    private UserRequestHistoryDao $userRequestHistoryDao;

    public function __construct(
        AuthService $authService,
        ApiService $apiService,
        UserRequestHistoryDao $userRequestHistoryDao
    ) {
        parent::__construct($authService);
        $this->apiService = $apiService;
        $this->userRequestHistoryDao = $userRequestHistoryDao;
    }

    public function actionGetCompanyCachePayment(GetCompanyOrderOptionsRequest $request)
    {
        $this->userRequestHistoryDao->createUserRequest(
            $request->all(),
            $request->getRequestUri(),
            $this->authService->getUser()
        );

        $payment = $this->apiService->getPayment($request);

        if (!$payment) {
            return $this->jsonError();
        } else {
            return $this->json(
                $payment->response
            );
        }
    }
}
