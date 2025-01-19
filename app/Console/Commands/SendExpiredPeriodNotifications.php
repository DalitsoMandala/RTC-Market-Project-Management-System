<?php

namespace App\Console\Commands;

use App\Jobs\SendExpiredPeriodNotificationJob;
use App\Models\Indicator;
use App\Models\Organisation;
use App\Models\ResponsiblePerson;
use App\Models\SubmissionPeriod;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendExpiredPeriodNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:send-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications for expired submission periods';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $periods = SubmissionPeriod::where('date_ending', '<', Carbon::now())
            ->where('is_expired', 0)
            ->get();

        foreach ($periods as $period) {
            $indicator = $period->indicator_id;
            $indicatorName = Indicator::find($indicator)->indicator_name;
            $people = ResponsiblePerson::where('indicator_id', $indicator)->pluck('organisation_id');
            $organisations = Organisation::with('users')->whereIn('id', $people)->get()->pluck('users');
            $users = $organisations->flatten();

            SendExpiredPeriodNotificationJob::dispatch($users, $indicatorName);

            $period->update([
                'is_expired' => 1,
                'is_open' => 0
            ]);

        }

    }
}