<?php

namespace App\Http\Controllers\Api;

use App\Db\Service\UserDao;
use App\Db\Service\UserRequestHistoryDao;
use App\Http\Controllers\BaseController;
use App\Http\Requests\User\CreateRequest;
use App\Http\Requests\User\GetHistoryListRequest;
use App\Http\Requests\User\GetListRequest;
use App\Http\Requests\User\SetAccessToApiRequest;
use App\Http\Requests\User\UpdatePasswordByUserRequest;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\PaginationResource;
use App\Service\AuthService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends BaseController
{
    /** @var UserDao $userDao */
    private $userDao;
    /** @var UserRequestHistoryDao $userRequestHistoryDao */
    private $userRequestHistoryDao;

    public function __construct(
        AuthService $authService,
        UserDao $userDao,
        UserRequestHistoryDao $userRequestHistoryDao
    ) {
        parent::__construct($authService);
        $this->userDao = $userDao;
        $this->userRequestHistoryDao = $userRequestHistoryDao;
    }

    public function actionUsersList(GetListRequest $request)
    {
        $query = $this->userDao->getListUserProfiles($request);

        return $this->json(
            new PaginationResource($query, $request->getPage())
        );
    }

    public function actionCreate(CreateRequest $request)
    {
        if (!Str::is($request->password, $request->repeatPassword)) {
            return $this->jsonError('Пароли не совпадают');
        }
        $user = $this->userDao->createNew($request);

        return $this->json(
            $user->load('userProfile')
        );
    }

    public function actionUpdate(UpdateRequest $request)
    {
        $user = $this->userDao->firstWithData($request->id);

        $user = $this->userDao->update($request, $user);

        return $this->json(
            $user->load('userProfile')
        );
    }

    public function actionUpdatePasswordByUser(UpdatePasswordByUserRequest $request)
    {
        $user = $this->user();
        if (!Str::is($request->newPassword, $request->repeatPassword)) {
            return $this->jsonError('Пароли не совпадают');
        }
        if (!Hash::check($request->oldPassword, $user->password)) {
            return $this->jsonError('Старый пароль не действителен');
        }

        $user->password = $request->newPassword;
        $user->save();

        return $this->json();
    }

    public function actionUpdatePassword(UpdatePasswordRequest $request)
    {
        $user = $this->userDao->firstWithData($request->id);
        if (!Str::is($request->newPassword, $request->repeatPassword)) {
            return $this->jsonError('Пароли не совпадают');
        }

        $user->password = $request->newPassword;
        $user->save();
        return $this->json();
    }

    public function actionSetAccessToApi(SetAccessToApiRequest $request)
    {
        $user = $this->userDao->firstWithData($request->id);

        if (!$user) {
            return $this->responseNotFound();
        }

        $user = $this->userDao->setAccessToApi($request, $user);

        return $this->json(
            $user->load('userProfile')
        );
    }

    public function actionDelete(int $id)
    {
        $user = $this->userDao->firstWithData($id);

        if (!$user) {
            return $this->responseNotFound();
        }

        try {
            $user->userProfile()->delete();
            $user->delete();
        } catch (\Exception $e) {
            return $this->jsonError();
        }

        return $this->json([], 204);
    }

    public function actionGetOne(int $id)
    {
        $user = $this->userDao->firstWithData($id);

        if (!$user) {
            return $this->responseNotFound();
        }

        return $this->json(
            $user
        );
    }

    public function actionRequestsHistory(GetHistoryListRequest $request)
    {
        $user = $this->userDao->firstWithData($request->userId);

        if (!$user) {
            return $this->responseNotFound();
        }

        $query = $this->userRequestHistoryDao->getUserRequestHistoryQuery($user);

        return $this->json(
            new PaginationResource($query, $request->getPage())
        );
    }

    public function actionGenerateToken(int $id)
    {
        $user = $this->userDao->firstWithData($id);

        if (!$user) {
            return $this->responseNotFound();
        }

        return $this->json(
            $user->getJWTToken()
        );
    }
}
