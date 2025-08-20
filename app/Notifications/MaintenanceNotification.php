<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\HtmlString;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class MaintenanceNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $secretKey;
    public function __construct($secretKey)
    {
        //
        $this->secretKey = $secretKey;
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
        return (new MailMessage)
        ->greeting('Hello ' . $notifiable->name)
                    ->line('The system is currently in maintenance mode. Please use the following secret key to resume operations: ' )
                    ->line(new HtmlString("<b>{$this->secretKey}</b>"))
                    ->action('Go to website', url('/'.$this->secretKey))
                    ->line('Thank you for using our application!');
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
