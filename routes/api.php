<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;
use Illuminate\Support\Facades\URL;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

if (app()->environment('production')) {
    URL::forceScheme('https');
}

Route::middleware('prepareJsonResponse')->group(function ()
{
    Auth::guard('api')->user(); // instance of the logged user
    Auth::guard('api')->check(); // if an user is authenticated
    Auth::guard('api')->id(); // the id of the authenticated user

    Route::middleware('api')->group(function ()
    {
        Route::post('login', [Api\Auth\LoginController::class, 'login']);
        Route::post('logout', [Api\Auth\LoginController::class, 'logout']);
    });

    Route::prefix('calculationQueue')->group(function ()
    {
        Route::post('callback', [Api\ScheduleCalculationController::class, 'actionLogCallback']);
    });

    Route::group(['middleware' => 'api.onlyAuthenticated'], function ()
    {
        Route::post('user/update/password', [Api\UserController::class, 'actionUpdatePasswordByUser']);

        Route::middleware('accessToApi')->group(function ()
        {
            Route::prefix('search')->group(function ()
            {
                Route::post('city', [Api\AddressController::class, 'actionSearchCity']);
                Route::post('street', [Api\AddressController::class, 'actionSearchStreet']);
            });

            Route::prefix('companies')->group(function ()
            {
                Route::post('list', [Api\CompaniesController::class, 'actionCompaniesList']);
            });

            Route::prefix('calculationQueue')->group(function ()
            {
                Route::post('scheduleCalculation',
                    [Api\ScheduleCalculationController::class, 'actionScheduleCalculation']);
                Route::post('status ', [Api\ScheduleCalculationController::class, 'actionGetStatus']);
            });

            Route::prefix('company')->group(function ()
            {
                Route::post('options', [Api\CompanyOptionsController::class, 'actionGetCompanyCacheOptions']);
                Route::post('terminals', [Api\CompanyTerminalController::class, 'actionGetCompanyCacheTerminal']);
                Route::post('paymentMethods', [Api\CompanyPaymentController::class, 'actionGetCompanyCachePayment']);
            });

            Route::post('getCalculation', [Api\CalculationController::class, 'actionGetCalculation']);
        });

        Route::middleware('role:admin')->group(function ()
        {
            Route::prefix('user')->group(function ()
            {
                Route::post('list', [Api\UserController::class, 'actionUsersList']);
                Route::post('requestHistory', [Api\UserController::class, 'actionRequestsHistory']);
                Route::get('{id}', [Api\UserController::class, 'actionGetOne']);
                Route::get('generateToken/{id}', [Api\UserController::class, 'actionGenerateToken']);
                Route::post('create', [Api\UserController::class, 'actionCreate']);
                Route::post('update', [Api\UserController::class, 'actionUpdate']);
                Route::post('updatePassword', [Api\UserController::class, 'actionUpdatePassword']);
                Route::post('setAccess', [Api\UserController::class, 'actionSetAccessToApi']);
                Route::get('delete/{id}', [Api\UserController::class, 'actionDelete']);
            });
        });
    });
});

