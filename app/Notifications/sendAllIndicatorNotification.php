<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class sendAllIndicatorNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $indicators;
    public function __construct($indicators)
    {
        //
        $this->indicators = $indicators;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Action Required: New Data Submission Periods Opened')
            ->line('New submission periods have been created for the following indicators. Please log in to the system to submit your data at your earliest convenience.')
            ->line('Kindly note the submission deadlines are displayed in the system to ensure timely compliance.')
            ->action('Access System', url('/'))
            ->line('---')
            ->line('**Indicators Requiring Submission**:');


        foreach ($this->indicators as $indicator) {
            $mail->line("- {$indicator['indicator_name']}");
        }






        $mail->line('Thank you for using our application!');
        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
