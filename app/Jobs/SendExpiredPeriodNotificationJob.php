<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Collection;
use App\Notifications\SendReminder;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\PeriodExpiredNotification;

class SendExpiredPeriodNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $users;
    protected $indicatorName;
    /**
     * Create a new job instance.
     *
     * @param Collection $users
     */
    public function __construct(Collection $users, $indicatorName)
    {
        $this->users = $users;
        $this->indicatorName = $indicatorName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->users as $user) {
            $user->notify(new SendReminder('Submission Period Expired', "The submission period for <b>{$this->indicatorName}</b> has expired."));
        }
    }
}
