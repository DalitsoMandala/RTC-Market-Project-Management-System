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
        $periods = SubmissionPeriod::with('form')->where('date_ending', '<=', date('Y-m-d'))
            ->where('is_expired', false)
            ->get();


        foreach ($periods as $period) {
            $indicator = $period->indicator_id;
            $indicatorName = Indicator::find($indicator)->indicator_name;

            $organisations = Indicator::find($indicator)->organisation;
            $people = $organisations->pluck('id');

            $users = Organisation::whereIn('id', $people)
                ->whereHas('users.roles', function ($role) {
                    $role->whereNotIn('name', ['admin', 'project_manager', 'manager']);
                })
                ->with(['users' => function ($user) {
                    $user->whereHas('roles', function ($role) {
                        $role->whereNotIn('name', ['admin', 'project_manager', 'manager']);
                    });
                }])
                ->get()
                ->pluck('users')
                ->flatten(); // Flatten the collection of collections into a single collection


            $filteredUsers = $users->filter(function ($user) use ($period) {
                $mailable =  MailingList::where('user_id', $user->id)->where('submission_period_id', $period->id)->exists();
                return $mailable;
            });



            Bus::chain([
                new SendExpiredPeriodNotificationJob($users, $indicatorName, $period->form->name)
            ])->dispatch();



            $period->update([
                'is_expired' => 1,
                'is_open' => 0
            ]);
        }
    }
}