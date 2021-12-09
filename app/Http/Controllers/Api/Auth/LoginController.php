<?php

namespace App\Http\Controllers\Api\Auth;

use App\Db\Entity\User;
use App\Db\Service\UserDao;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use App\Service\AuthService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

// to api methods
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Psy\Util\Str;

class LoginController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /** @var UserDao $userDao */
    protected $userDao;

    /** @var AuthService $authService */
    protected $authService;

    public function __construct(UserDao $userDao, AuthService $authService)
    {
        parent::__construct($authService);
        $this->middleware('guest')->except('logout');
        $this->userDao = $userDao;
        $this->authService = $authService;
    }

    public function login(LoginRequest $request)
    {
        $this->validateLogin($request);

        $user = $this->userDao->getUserByEmail($request->email);

        if ($user && password_verify($request->password, $user->password)) {
            auth()->setUser($user);

            return $this->json($user);
        }

        return $this->sendFailedLoginResponse($request);
    }

    public function logout()
    {
        if (!$this->authService->checkTokenAndLogin()) {
            return $this->responseAccessDenied();
        }

        return $this->json();
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'email';
    }
}
