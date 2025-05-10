<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\URL;
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
        if (app()->environment('local')) {
            URL::forceScheme('http');
        }
        RedirectIfAuthenticated::redirectUsing(function ($request) {
            $user = User::find(auth()->user()->id);


            // Check for admin role
            if ($user->hasAnyRole('admin')) {
                return '/admin/dashboard';
            }
            if ($user->hasAnyRole('staff')) {
                return '/staff/dashboard';
            }
            if ($user->hasAnyRole('project_manager')) {
                return '/cip/project-manager/dashboard';
            }

            if ($user->hasAnyRole('manager')) {

                return '/cip/dashboard';
            }



            // Check for external users
            if ($user->hasAnyRole('external')) {
                return '/external/dashboard';
            }
        });
    }
}
