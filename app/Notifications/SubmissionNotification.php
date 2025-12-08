<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SubmissionNotification extends Notification implements ShouldQueue
{
    use Queueable;
    public $status;
    public $denialMessage;
    public $typeOfForm;
    public $message;
    public $batchId;
    public $link;

    /**
     * Create a new notification instance.
     */
    public function __construct($status = null, $denialMessage = null, $batchId = null, $link = null, $form = null)
    {
        $this->status = $status;
        $this->denialMessage = $denialMessage;
        $this->batchId = $batchId;
        $this->link = $link;
        $this->typeOfForm = $form;
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
                ->line('Congratulations! Your submission has been **accepted!**')
                ->line('Batch No. ' . $this->batchId)
                ->line('Form: ' . $this->typeOfForm)
                ->action('Go to website', $this->link);
        } else {

            if ($this->denialMessage === 'NA') {
                $mailMessage
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->subject('Submission Denied')
                    // Use Markdown for bold/underline (which looks like **bold** in plain text)
                    ->line('We regret to inform you that your submission has been **disapproved!**')
                    // ->line() automatically creates new paragraphs, ensuring a break here
                    ->line('Form: ' . $this->typeOfForm)
                    ->action('Go to website', $this->link)
                ;

                return $mailMessage;
            }
            $mailMessage
                ->greeting('Hello ' . $notifiable->name . ',')
                ->subject('Submission Denied')
                ->line('We regret to inform you that your submission has been **disapproved!**')
                ->line('Reason for denial: ')
                ->line('**' . $this->denialMessage . '**')
                // Laravel converts this new paragraph into clean HTML spacing:
                ->line('Form: ' . $this->typeOfForm)
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
