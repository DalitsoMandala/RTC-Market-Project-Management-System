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

            $user = User::find(auth()->user()->id);

            if ($user->hasAnyRole('admin')) {

                return '/admin/dashboard';
            } else if ($user->hasAnyRole('internal')) {

                //internal users

                if ($user->hasAnyRole('cip')) {

                    if ($user->hasAnyRole('staff')) {
                        return '/staff/dashboard';
                    }
                    return '/cip/dashboard';

                } else

                    if ($user->hasAnyRole('desira')) {
                        return '/desira/dashboard';

                    }

            } else if ($user->hasAnyRole('external')) {

                if ($user->hasAnyRole('donor')) {
                    return '/external/executive';
                } else {

                    return '/external/dashboard';
                }


            }

        });
    }
}
