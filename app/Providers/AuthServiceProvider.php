<?php

namespace App\Providers;

use App\Db\Entity\Appointment;
use App\Db\Entity\AppointmentType;
use App\Db\Entity\Patient;
use App\Policies\Appointment\AppointmentPolicy;
use App\Policies\Appointment\AppointmentTypePolicy;
use App\Policies\Patient\PatientPolicy;
use App\Service\Guard\JWTGuard;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Auth::extend('jwt', function ($app, $name, array $config)
        {
            return new JWTGuard(Auth::createUserProvider($config['provider']), $app['request']);
        });
    }
}
