<?php

namespace App\Providers;

use App\Helpers\RoleBasedRedirectHelper;
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

            $helper = new RoleBasedRedirectHelper($user);

          return  $helper->getDashboardRoute($user);

        });
    }
}
