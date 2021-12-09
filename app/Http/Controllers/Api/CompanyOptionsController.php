<?php

namespace App\Http\Controllers\Api;

use App\Db\Service\UserRequestHistoryDao;
use App\Http\Controllers\BaseController;
use App\Http\Requests\GetCompanyOrderOptionsRequest;
use App\Http\Service\CargoGuru\ApiService;
use App\Service\AuthService;

class CompanyOptionsController extends BaseController
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

    public function actionGetCompanyCacheOptions(GetCompanyOrderOptionsRequest $request)
    {
        $this->userRequestHistoryDao->createUserRequest(
            $request->all(),
            $request->getRequestUri(),
            $this->authService->getUser()
        );

        $options = $this->apiService->getOptions($request);

        if (!$options) {
            return $this->jsonError();
        } else {
            return $this->json(
                $options->response
            );
        }
    }
}
