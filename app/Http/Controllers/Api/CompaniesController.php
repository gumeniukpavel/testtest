<?php

namespace App\Http\Controllers\Api;

use App\Db\Service\CompaniesCacheDao;
use App\Db\Service\UserRequestHistoryDao;
use App\Http\Controllers\BaseController;
use App\Http\Requests\CompaniesCache\GetListRequest;
use App\Http\Resources\PaginationResource;
use App\Service\AuthService;

class CompaniesController extends BaseController
{
    /** @var CompaniesCacheDao $companiesCacheDao */
    private $companiesCacheDao;
    /** @var UserRequestHistoryDao $userRequestHistoryDao */
    private $userRequestHistoryDao;

    public function __construct(
        AuthService $authService,
        CompaniesCacheDao $companiesCacheDao,
        UserRequestHistoryDao $userRequestHistoryDao
    ) {
        parent::__construct($authService);
        $this->companiesCacheDao = $companiesCacheDao;
        $this->userRequestHistoryDao = $userRequestHistoryDao;
    }

    public function actionCompaniesList(GetListRequest $request)
    {
        $this->userRequestHistoryDao->createUserRequest(
            $request->all(),
            $request->getRequestUri(),
            $this->authService->getUser()
        );
        $query = $this->companiesCacheDao->listQuery();

        return $this->json(
            new PaginationResource($query, $request->getPage())
        );
    }
}
