<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BatchDataAddedNotification extends Notification
{
    use Queueable;
    public $batch_no, $link;
    /**
     * Create a new notification instance.
     */
    public function __construct($batch_no, $link)
    {
        //'
        $this->batch_no = $batch_no;
        $this->link = $link;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
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
            'message' => 'New batch record has been added.',
            'batch_no' => $this->batch_no,
            'link' => $this->link,

        ];
    }

    public function databaseType(object $notifiable): string
    {
        return 'batch_data_added';
    }
}
