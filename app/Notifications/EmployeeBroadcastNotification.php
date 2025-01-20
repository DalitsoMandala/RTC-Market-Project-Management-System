<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class EmployeeBroadcastNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $messageContent;
    public $link;

    public $error = false;

    public function __construct($messageContent, $link, $error = false)
    {
        $this->messageContent = $messageContent;
        $this->link = $link;
        $this->error = $error; // Assume there are no errors at first
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        if ($this->error === true) {
            return (new MailMessage)
                ->greeting('Hello ' . $notifiable->name . ',')
                ->subject('Important Update - Error!')
                ->line(new HtmlString($this->messageContent));
        }

        return (new MailMessage)
            ->subject('Submissions Notification!')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line(new HtmlString($this->messageContent))
            ->action('Go to website', $this->link)  // Adding a call-to-action button
            ->line('Thank you for your attention!');
    }
}