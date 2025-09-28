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
use Illuminate\Support\Facades\Gate;
use Opcodes\LogViewer\LogFile;
use Opcodes\LogViewer\LogFolder;
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



    /**
     * The deleteLogFile gate determines if a user can delete an individual log file.
     * By returning false, you disable the delete button for all users.
     */
    Gate::define('deleteLogFile', function (?User $user, LogFile $file) {
        // Return false to prevent ALL log files from being deleted.
        return false;
    });

    /**
     * The deleteLogFolder gate determines if a user can delete a log group (folder).
     * This is also important to secure.
     */
    Gate::define('deleteLogFolder', function (?User $user, LogFolder $folder) {
        // Return false to prevent ALL log folders/groups from being deleted.
        return false;
    });

    }
}
