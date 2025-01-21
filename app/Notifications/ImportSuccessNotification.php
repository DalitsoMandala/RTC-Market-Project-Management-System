<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ImportSuccessNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected $message;
    protected $uuid, $link;
    protected $errors, $sheet;
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
        return ['mail', 'database']; // Or 'database', 'slack', etc., based on your needs
    }

    public function toMail($notifiable)
    {

        return (new MailMessage)
            ->subject('Import Successful')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($this->message)
            ->action('View Details', $this->link) // Adjust URL
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
