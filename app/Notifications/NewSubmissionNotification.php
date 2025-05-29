<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Artisan;

class NewSubmissionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $submissionDetails;
    public $user;
    public $prefix;
    /**
     * Create a new notification instance.
     *
     * @param array $submissionDetails
     */
    public function __construct($prefix)
    {

        $this->prefix = $prefix;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->subject('New Submission for Review')
            ->line('A new submission has been made that requires your review.')
            ->action('Review Submission', url($this->prefix . '/submissions'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {

        return [
            'message' => 'A new submission has been made that requires your review.',
            'link' => url($this->prefix . '/submissions'),
        ];
    }

    public function databaseType($notifiable)
    {
        return 'submissions';
    }
}
