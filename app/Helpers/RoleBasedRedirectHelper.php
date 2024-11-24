<?php

namespace App\Helpers;

class RoleBasedRedirectHelper
{
    protected $user;
  public function __construct($user){
    $this->user = $user;
  }


    public static function getDashboardRoute($user): string
    {


        // Check for admin role
        if ($user->hasAnyRole('admin')) {
            return '/admin/dashboard';
        }

        // Check for internal users
        if ($user->hasAnyRole('internal')) {
            // Internal CIP users
            if ($user->hasAnyRole('cip')) {
                if ($user->hasAnyRole('staff')) {
                    return '/staff/dashboard';
                } elseif ($user->hasAnyRole('project_manager')) {
                    return '/cip/project-manager/dashboard';
                }
                return '/cip/dashboard';
            }

            // Internal Desira users
            if ($user->hasAnyRole('desira')) {
                return $user->hasAnyRole('staff')
                    ? '/desira-staff/dashboard'
                    : '/desira/dashboard';
            }
        }

        // Check for external users
        if ($user->hasAnyRole('external')) {
            return '/external/dashboard';
        }

        // Default fallback
        return '/';
    }
}
