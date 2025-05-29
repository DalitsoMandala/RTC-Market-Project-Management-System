<?php

namespace App\Traits;

use App\Models\User;
use App\Notifications\NewSubmissionNotification;

trait NotifyAdmins
{
    protected function notifyAdminsAndManagers()
    {

        $users = User::with('roles')->whereHas('roles', function ($role) {
            $role->whereIn('name', ['admin', 'manager']);
        })->get();

        // Notify each user
        foreach ($users as $user) {
            // Determine the prefix based on the user's role
            $prefix = $user->hasAnyRole('admin') ? '/admin' : '/cip';

            // Send notification
            $user->notify(new NewSubmissionNotification($prefix));
        }
    }
}
