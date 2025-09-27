<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Helpers\RoleBasedRedirectHelper;
use Opcodes\LogViewer\Facades\LogViewer;
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

        LogViewer::auth(function ($request) {
            if (Auth::check()) {
                $user = User::find(Auth::user()->id);
                return $user && $user->hasAnyRole(['admin', 'monitor']); // only admins
            }

            return false;
        });
    }
}
