<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Indicator;
use Illuminate\Console\Command;
use App\Models\SubmissionPeriod;
use App\Jobs\sendReminderToUserJob;
use Illuminate\Support\Facades\Bus;
use App\Notifications\SubmissionReminder;
use App\Traits\GroupsEndingSoonSubmissionPeriods;

class CheckSubmissionDeadlines extends Command
{
    protected $signature = 'check:submission-deadlines';
    protected $description = 'Check submission deadlines and send reminders to users';
    use GroupsEndingSoonSubmissionPeriods;

    public function handle()
    {

        $users =   $this->getUserWithDeadlines(3, false);
        foreach ($users as $userId => $userData) {


            $indicators = $userData['user']->organisation->indicatorResponsiblePeople->map(function ($indicator) {

                return Indicator::find($indicator->indicator_id)->indicator_name;
            })->flatten();


            $userData['indicators'] = $indicators;

            Bus::chain([
                new sendReminderToUserJob($userData['user'], $userData)
            ])->dispatch();
        }
        $this->info('Submission reminders sent successfully.');
    }
}
