<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\SubmissionPeriodsEndingSoon;

class sendReminderToUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $user, $period;
    public function __construct($user, $period)
    {
        //
        $this->user = $user;
        $this->period = $period;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //


        $this->user->notify(new SubmissionPeriodsEndingSoon($this->period));
    }
}
