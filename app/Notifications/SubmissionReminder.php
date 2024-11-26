<?php

namespace App\Notifications;

use App\Models\Indicator;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SubmissionReminder extends Notification
{
    use Queueable;

    public $submissionPeriod;

    public function __construct($submissionPeriod)
    {
        $this->submissionPeriod = $submissionPeriod;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {

        $name = Indicator::find($this->submissionPeriod->indicator_id)->indicator_name;
        return (new MailMessage)
            ->subject('Submission Deadline Reminder')
            ->line('This is a reminder that submissions for "' . $name . '" are due on ' . Carbon::parse($this->submissionPeriod->date_ending)->format('d-m-Y') . ' at ' . Carbon::parse($this->submissionPeriod->date_ending)->format('h:i A'))
            ->action('Submit Now', url('/'))
            ->line('Thank you for your attention!');
    }
}