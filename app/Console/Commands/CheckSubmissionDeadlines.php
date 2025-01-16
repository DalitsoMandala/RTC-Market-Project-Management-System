<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Indicator;
use Illuminate\Console\Command;
use App\Models\SubmissionPeriod;
use Illuminate\Support\Facades\Bus;
use App\Notifications\SubmissionReminder;

class CheckSubmissionDeadlines extends Command
{
    protected $signature = 'check:submission-deadlines';
    protected $description = 'Check submission deadlines and send reminders to users';


    public function handle()
    {
        $today = Carbon::now();



        // Query submissions 3 days before and on the last day
        $submissionPeriods = SubmissionPeriod::whereDate(
            'date_ending',
            $today->copy()->addDays(3)->toDateString(),

        )->orWhereDate('date_ending', $today->toDateString())->get();


        foreach ($submissionPeriods as $submissionPeriod) {

            $indicator = Indicator::find($submissionPeriod->indicator_id);
            $organisations = $indicator->responsiblePeopleforIndicators()->pluck('organisation_id');
            $users = User::with('organisation')->whereHas('organisation', function ($query) use ($organisations) {
                $query->whereIn('id', $organisations);
            })->get();




            foreach ($users as $user) {
                Bus::chain([
                    fn() =>   $user->notify(new SubmissionReminder($submissionPeriod))
                ])->dispatch();
            }
        }

        $this->info('Submission reminders sent successfully.');
    }
}
