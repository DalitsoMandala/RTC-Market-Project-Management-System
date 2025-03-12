<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserNotification extends Notification implements ShouldQueue
{

    use Queueable;

    protected $email;
    protected $password;

    /**
     * Create a new notification instance.
     */
    public function __construct($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail']; // Send via email
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your New Account Details')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your account has been created successfully.')
            ->line('Here are your login credentials:')
            ->line('**Email:** ' . $this->email)
            ->line('**Password:** ' . $this->password)
            ->action('Login Here', url('/login'))
            ->line('Please change your password after logging in.')
            ->line('Thank you for using our application!');
    }
}
