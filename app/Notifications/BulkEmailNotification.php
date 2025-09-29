<?php

namespace App\Notifications;


use Illuminate\Support\HtmlString;
use Illuminate\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class BulkEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;
    public $subject;
    public $message;
    /**
     * Create a new notification instance.
     */
    public function __construct($subject, $message)
    {
        //
        $this->subject = $subject;
        $this->message = $message;
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
        ->greeting('')
            ->subject($this->subject)
            ->line(new HtmlString($this->message))
            ->action('Access System', url('/'))
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
