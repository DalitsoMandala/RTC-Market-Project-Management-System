<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubmissionNotification extends Notification implements ShouldQueue
{
    use Queueable;
    public $status;
    public $denialMessage;


    public $batchId;

    /**
     * Create a new notification instance.
     */
    public function __construct($status, $denialMessage = null, $batchId = null)
    {
        $this->status = $status;
        $this->denialMessage = $denialMessage;
        $this->batchId = $batchId;
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

        $mailMessage = new MailMessage();
        if ($this->status === 'accepted') {
            $mailMessage
                ->greeting('Hello ' . $notifiable->name . ',')
                ->subject('Submission Accepted')
                ->line('Congratulations! Your submission has been accepted. Batch No. ' . $this->batchId);
        } else {
            $mailMessage

                ->greeting('Hello ' . $notifiable->name . ',')
                ->subject('Submission Denied')
                ->line('We regret to inform you that your submission has been denied.')
                ->line('Reason for denial: ' . $this->denialMessage);
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
        return [
            //
        ];
    }
}
