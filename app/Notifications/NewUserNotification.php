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
    protected $role;
    /**
     * Create a new notification instance.
     */
    public function __construct($email, $password, $role = null)
    {
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
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
        $role = $this->role; // assuming 'role' is a property on the user
        $message = (new MailMessage)
            ->subject('Welcome! Your Account Is Ready')
            ->greeting('Hello ' . $notifiable->name . ',');

        $message->line('Your account has been successfully created and is now active.')
            ->line('Here are your login credentials:')
            ->line('**Email:** ' . $this->email)
            ->line('**Password:** ' . $this->password)
            ->action('Access Your Account', url('/login'));

        if ($role === 'project_manager') {
            $message->line('As a manager, you can now monitor project activity and view progress dashboards.');
        }
        if ($role === 'manager' || $role === 'admin') {
            $message->line('As a manager, you can now monitor project activity, track submissions, and view progress dashboards as well as other functionalities');
        } else {
            $message->line('You can now log in and start submitting your data through the system.');
        }

        $message->line('For security reasons, please change your password after your first login.')
            ->line('Welcome aboard!');

        return $message;
    }
}
