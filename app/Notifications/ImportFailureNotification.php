<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\HtmlString;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ImportFailureNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $error;
    protected $link;
    protected $uuid;

    public function __construct($error, $link, $uuid)
    {
        $this->error = $error;
        $this->link = $link;
        $this->uuid = $uuid;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Import Failed')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Unfortunately, your import has failed. Please try again')
            ->line(new HtmlString("Batch ID: <b>{$this->uuid}</b>"))  // Adding batch ID to error message
            ->line(new HtmlString('Error: <span style="color:red; font-weight: bold">' . $this->error . '</span>'))
            ->action('Go to Website', url('/'))
            ->line('Please try again.');
    }

    public function toArray(object $notifiable): array
    {
        return [

            'uuid' => $this->uuid,
            'message' => $this->error,
            'link' => url('/'),
        ];
    }
    public function databaseType(object $notifiable): string
    {
        return 'failed_imports';
    }
}