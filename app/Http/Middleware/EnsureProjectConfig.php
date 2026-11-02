<?php

namespace App\Http\Middleware;

use App\Services\ProjectService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureProjectConfig
{
    public function handle(Request $request, Closure $next): Response
    {

        // 1. If config is cached but config/app.php was edited afterwards,
        //    clear the cache and reload the request so fresh values are used.
        if ($this->configIsStale() && ! $request->attributes->get('config_refreshed')) {
            Artisan::call('config:clear');

            return redirect()->to($request->fullUrl());
        }

        // 2. Make sure the configured projects actually exist and are active.
        if (! config('app.currentProject') || ! ProjectService::app_project()) {
            abort(500, 'Current project is not configured or is not active. Check config/app.php (currentProject).');
            Log::error('Current project is not configured or is not active. Check config/app.php (currentProject).');
        }

        if (config('app.currentCgiarProject') && ! ProjectService::app_cgiar_project()) {
            abort(500, 'Current CGIAR project is not configured or is not active. Check config/app.php (currentCgiarProject).');
            Log::error('Current CGIAR project is not configured or is not active. Check config/app.php (currentCgiarProject).');
        }

        return $next($request);
    }

    private function configIsStale(): bool
    {
        if (app()->isProduction() || ! app()->configurationIsCached()) {
            return false;
        }

        $cached = app()->getCachedConfigPath();
        $source = config_path('app.php');

        return is_file($cached) && is_file($source) && filemtime($source) > filemtime($cached);
    }
}
