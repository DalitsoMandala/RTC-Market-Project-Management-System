<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SubmissionPeriodsEndingSoon extends Notification
{
    use Queueable;

    public array $periods;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $periods)
    {
        $this->periods = $periods;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject('Submission Periods Ending Soon')
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('The following submission periods for these indicators will end soon. Please take action before the deadline.');
        $mail->line("**Their Dates include:** ");
        foreach ($this->periods['periods'] as $date) {
            $mail->line("- {$date['start']} to {$date['end']}");
        }

        $mail->line('**Indicators Requiring Submission**:');

        foreach ($this->periods['indicators'] as $indicator) {
            $mail->line("- {$indicator}");
        }
        $mail->action('Submit Now', url('/submissions'))
            ->line('Thank you for your attention.');

        return $mail;
    }
}
