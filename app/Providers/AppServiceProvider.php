<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;

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
        //

        RedirectIfAuthenticated::redirectUsing(function ($request) {
            $user = auth()->user();

            // Check for admin role
            if ($user->hasAnyRole('admin')) {
                return '/admin/dashboard';
            }

            // Check for internal users
            if ($user->hasAnyRole('internal')) {
                // Internal CIP users
                if ($user->hasAnyRole('cip')) {
                    if ($user->hasAnyRole('staff')) {
                        return '/staff/dashboard';
                    } elseif ($user->hasAnyRole('project_manager')) {
                        return '/cip/project-manager/dashboard';
                    }
                    return '/cip/dashboard';
                }


                // Internal Desira users
                if ($user->hasAnyRole('desira')) {
                    return $user->hasAnyRole('staff')
                        ? '/desira-staff/dashboard'
                        : '/desira/dashboard';
                }
            }

            // Check for external users
            if ($user->hasAnyRole('external')) {
                return '/external/dashboard';
            }


        });

    }
}
