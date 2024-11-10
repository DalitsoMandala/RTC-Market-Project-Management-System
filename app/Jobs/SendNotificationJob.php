<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\EmployeeBroadcastNotification;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $user;
    protected $messageContent;
    protected $link;

    protected $error = false;
    /**
     * Create a new job instance.
     */
    public function __construct(User $user, $messageContent, $link, $error = false)
    {
        $this->user = $user;
        $this->messageContent = $messageContent;
        $this->link = $link;
        $this->error = $error;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->user->notify(new EmployeeBroadcastNotification($this->messageContent, $this->link, $this->error));
    }
}
