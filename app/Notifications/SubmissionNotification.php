<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SubmissionNotification extends Notification
{
    use Queueable;
    public $status;
    public $denialMessage;

    public $message;
    public $batchId;
    public $link;

    /**
     * Create a new notification instance.
     */
    public function __construct($status, $denialMessage = null, $batchId = null, $link)
    {
        $this->status = $status;
        $this->denialMessage = $denialMessage;
        $this->batchId = $batchId;
        $this->link = $link;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {

        $mailMessage = new MailMessage();
        if ($this->status === 'approved') {
            $mailMessage
                ->greeting('Hello ' . $notifiable->name . ',')
                ->subject('Submission Accepted')
                ->line('Congratulations! Your submission has been accepted. Batch No. ' . $this->batchId)

                ->action('Go to website', $this->link);
            Artisan::call('update:information');
        } else {
            $mailMessage

                ->greeting('Hello ' . $notifiable->name . ',')
                ->subject('Submission Denied')
                ->line('We regret to inform you that your submission has been denied.')
                ->line('Reason for denial: ')
                ->line(new HtmlString('<b style="color:red;">' . $this->denialMessage . '</b>'))
                ->action('Go to website', $this->link)
            ;
        }



        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        if ($this->denialMessage) {
            $this->message = 'We regret to inform you that your submission has been denied. Reason for denial: ' . new HtmlString('<b>' . $this->denialMessage . '</b>');
        } else {
            $this->message = 'Your submission has been accepted. Batch No. ' . $this->batchId;
        }

        return [
            //
            'message' => $this->message,
            'link' => $this->link,
            //
        ];
    }

    public function databaseType($notifiable)
    {
        return 'submissions';
    }
}
