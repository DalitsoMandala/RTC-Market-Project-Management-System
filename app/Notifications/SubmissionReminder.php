<?php

namespace App\Notifications;

use App\Models\Indicator;
use App\Models\SubmissionPeriod;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

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

        $indicato_name = Indicator::find($this->submissionPeriod->indicator_id)->indicator_name;
        $form = SubmissionPeriod::find($this->submissionPeriod->id)->form->name;
        $name = "<b>{$form}</b>" . " (<em>Indicator Name for this form: {$indicato_name}</em>)";
        return (new MailMessage)
            ->subject('Submission Deadline Reminder')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line(new HtmlString(
                'This is a reminder that submissions for "' . $name . '" are due on <span style="color:red; font-weight: bold">' . Carbon::parse($this->submissionPeriod->date_ending)->format('d-m-Y') . ' at ' . Carbon::parse($this->submissionPeriod->date_ending)->format('h:i A') . '</span>'
            ))
            ->action('Submit Now', url('/'))
            ->line('Thank you for using our application!');
    }
}
