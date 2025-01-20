<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ImportSuccessNotification extends Notification
{
    use Queueable;
    public $message;
    public $uuid, $link;
    public $errors, $sheet;
    /**
     * Create a new notification instance.
     */
    public function __construct($uuid, $link, $message = 'Your import has been processed successfully.')
    {
        //


        $this->uuid = $uuid;
        $this->message = $message;
        $this->link = $link;
    }

    public function via($notifiable)
    {
        return ['mail']; // Or 'database', 'slack', etc., based on your needs
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Import Successful')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($this->message)
            ->action('View Details', url($this->link)) // Adjust URL
            ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        return [

            'uuid' => $this->uuid,
            'message' => $this->message,
            'link' => $this->link,
        ];
    }
    public function databaseType(object $notifiable): string
    {
        return 'imports';
    }
}