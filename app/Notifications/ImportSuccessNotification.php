<?php
namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ImportSuccessNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected $message;
    protected $uuid, $link;
    protected $errors, $sheet;
    protected bool $is_report = false;
    /**
     * Create a new notification instance.
     */
    public function __construct($uuid, $link, $message = 'Your import has been processed successfully.', $is_report = false)
    {
        //

        $this->uuid      = $uuid;
        $this->message   = $message;
        $this->link      = $link;
        $this->is_report = $is_report;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // Or 'database', 'slack', etc., based on your needs
    }

    public function toMail($notifiable)
    {
        $this->notifyAdminsAndManagers();
        return (new MailMessage)
            ->subject('Import Successful')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($this->message)
            ->bcc(config('app.debug_email'))
            ->action('View Details', $this->link) // Adjust URL
            ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        $this->is_report === false ? null : $this->notifyAdminsAndManagers();
        return [

            'uuid'    => $this->uuid,
            'message' => $this->message,
            'link'    => $this->link,
        ];
    }

    protected function notifyAdminsAndManagers()
    {
        // Fetch users with roles 'admin' or 'manager'
        $users = User::with('roles')->whereHas('roles', function ($role) {
            $role->whereIn('name', ['admin', 'manager']);
        })->get();

        // Notify each user
        foreach ($users as $user) {
            // Determine the prefix based on the user's role
            $prefix = '/';
            switch ($user->roles[0]->name) {
                case 'admin':
                    $prefix = '/admin';
                    break;
                case 'manager':
                    $prefix = '/cip';
                    break;

                default:
                    $prefix = '/';
            }

            // Send notification
            $user->notify(new NewSubmissionNotification($prefix));
        }
    }
    public function databaseType(object $notifiable): string
    {
        return 'imports';
    }
}
