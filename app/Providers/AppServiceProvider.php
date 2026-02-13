<?php

namespace App\Providers;

use App\Modules\User\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 🔥 Superadmin bypass GLOBAL
        Gate::before(function (User $user, string $ability) {
            // Superadmin bypass total
            if ($user->{User::EMAIL} === 'recepcionista@pruebasmulhacen.com') {
                return true;
            }

            return null;
        });

        $permissions = [
            'patient.create',
            'appointment.schedule',
            'appointment.create',
        ];

        foreach ($permissions as $action) {
            Gate::define(
                $action,
                function (User $user) use ($action) {
                    return false;
                }
            );
        }
    }
}
