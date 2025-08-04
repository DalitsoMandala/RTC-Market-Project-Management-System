<?php

namespace App\Helpers;

class RoleBasedRedirectHelper
{
    protected $user;
    public function __construct($user)
    {
        $this->user = $user;
    }


    public static function getDashboardRoute($user): string
    {


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

        if ($user->hasAnyRole('enumerator')) {

            return '/enumerator/dashboard';
        }


        // Check for external users
        if ($user->hasAnyRole('external') || $user->hasAnyRole('external_manager')) {
            return '/external/dashboard';
        }

        // Default fallback
        return '/';
    }
}
