<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\sendAllIndicatorNoification;
use App\Notifications\sendAllIndicatorNotification;

class sendAllIndicatorNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $indicators;
    public $user;
    public function __construct($user, $indicators)
    {
        //
        $this->indicators = $indicators;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $this->user->notify(new sendAllIndicatorNotification($this->indicators));
    }
}
