<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
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
        //

        RedirectIfAuthenticated::redirectUsing(function ($request) {

            $user = auth()->user();

            if ($user->hasAnyRole('admin')) {

                return redirect(route('admin-dashboard'));
            } else if ($user->hasAnyRole('internal')) {

                //internal users

                if ($user->hasAnyRole('cip')) {

                    return route('cip-internal-dashboard');

                } else

                    if ($user->hasAnyRole('desira')) {
                        return route('desira-dashboard');

                    }


            } else {

                if ($user->hasAnyRole('cip')) {
                    return route('cip-external-dashboard');

                } else

                    if ($user->hasAnyRole('desira')) {
                        return route('desira-external-dashboard');

                    }



            }

        });
    }
}