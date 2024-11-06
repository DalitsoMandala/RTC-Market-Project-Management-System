<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\Exceptions\UnauthorizedException;

class CheckAllRoles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Check if the user has all of the specified roles
        if (!$request->user() || !$request->user()->hasAllRoles($roles)) {
            throw UnauthorizedException::forRoles($roles);
        }

        return $next($request);
    }
}
