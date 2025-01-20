<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Baseline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class CheckBaselineValues
{
    public function handle(Request $request, Closure $next): Response
    {
        // Count indicators with non-null baseline values
        $filledIndicatorsCount = Baseline::whereNotNull('baseline_value')->count();

        // Check if there are 52 indicators with non-null values
        if ($filledIndicatorsCount < 52) {
            // Redirect to baseline data page for privileged roles if values are incomplete
            if (
                auth()->check() &&
                ((auth()->user()->hasAllRoles([

                    'manager',
                    'cip'
                ])) || auth()->user()->hasRole('admin'))
            ) {

                if ($request->routeIs('baseline') || $request->is('*baseline*')) {
                    return $next($request); // Allow the request if already on the baseline page
                }
                $routePrefix = Route::current()->getPrefix();
                return redirect()->to($routePrefix . '/baseline');
            } else {

                return abort(503);
            }

            // Show "Site Under Maintenance" page for regular users

        }

        return $next($request);
    }
}