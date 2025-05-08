<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Indicator;
use App\Models\MailingList;
use App\Models\Organisation;
use Illuminate\Console\Command;
use App\Models\SubmissionPeriod;
use App\Models\ResponsiblePerson;
use Illuminate\Support\Facades\Bus;
use App\Jobs\SendExpiredPeriodNotificationJob;
use App\Traits\GroupsEndingSoonSubmissionPeriods;

class SendExpiredPeriodNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:expired-period-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications for expired submission periods';

    /**
     * Execute the console command.
     */
    use GroupsEndingSoonSubmissionPeriods;
    public function handle()
    {
        $sendExpired = true;

        $users =   $this->getUserWithDeadlines(3, $sendExpired);

        if ($sendExpired) {
            foreach ($users as $userId => $userData) {


                $indicators = $userData['user']->organisation->indicatorResponsiblePeople->map(function ($indicator) {

                    return Indicator::find($indicator->indicator_id)->indicator_name;
                })->flatten();


                $userData['indicators'] = $indicators;

                Bus::chain([
                    new SendExpiredPeriodNotificationJob($userData['user'], $userData)
                ])->dispatch();
            }
        }
    }
}
