<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PeriodExpiredNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $periods;
    public function __construct($periods)
    {
        //
        $this->periods = $periods;
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
    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject('Expired Submission Period Notification')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line("The submission period for these indicators has expired.")
            ->action('Access System', url('/'))
            ->line('---')
            ->line('**Indicators Expired**:');

        foreach ($this->periods['indicators'] as $indicator) {
            $mail->line("- {$indicator}");
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
