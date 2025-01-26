<?php

namespace App\Traits;

use App\Models\User;
use App\Notifications\NewSubmissionNotification;

trait NotifyAdmins
{
    protected function notifyAdminsAndManagers()
    {


        $users = User::with('roles')->whereHas('roles', function ($role) {
            $role->where('name', 'admin')->orWhere('name', 'manager');
        })->get();
        foreach ($users as $user) {
            $user->notify(new NewSubmissionNotification($user));
        }
    }
}
