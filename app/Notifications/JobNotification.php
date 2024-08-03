<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class JobNotification extends Notification
{
    use Queueable;
    public $message, $uuid;
    protected $errors, $sheet;
    /**
     * Create a new notification instance.
     */
    public function __construct($uuid, $message, $errors, $sheet = null)
    {
        //


        $this->uuid = $uuid;
        $this->message = $message;
        $this->errors = $errors;
        $this->sheet = $sheet;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->line($this->message)
            ->line('Thank you for using our application!')
            ->subject('Data Import');

        if (!empty($this->errors)) {
            Log::info($this->errors);
            $fileName = 'errors_' . now()->format('Y_m_d_H_i_s') . '.txt';
            $filePath = 'errors/' . $fileName; // Storage path within the 'storage/app' directory

            $errorContent = $this->formatErrors($this->errors, $this->sheet);

            // Ensure the directory exists
            Storage::makeDirectory('errors');

            // Write errors to the file
            Storage::put($filePath, $errorContent);

            // Attach the file to the email
            $mailMessage->attach(storage_path('app/' . $filePath), [
                'as' => $fileName,
                'mime' => 'text/plain',
            ]);

            $mailMessage->line('You can find the errors in the file attached.');
        }

        return $mailMessage;
    }

    protected function formatErrors(array $errors, string $sheet): string
    {
        $formattedErrors = 'Sheet: ' . $sheet . PHP_EOL . str_repeat('-', 20) . PHP_EOL;

        foreach ($errors as $error) {
            $formattedErrors .= "Row: " . $error->row . PHP_EOL;
            $formattedErrors .= "Attribute: " . $error->attribute . PHP_EOL;
            $formattedErrors .= "Errors: " . implode(', ', $error->errors) . PHP_EOL;
            $formattedErrors .= "Values: " . PHP_EOL;

            foreach ((array) $error->values as $key => $value) {
                $formattedErrors .= "  $key: $value" . PHP_EOL;
            }

            $formattedErrors .= str_repeat('-', 20) . PHP_EOL;
        }

        return $formattedErrors;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [

            'uuid' => $this->uuid,
            'message' => $this->message,
        ];
    }
    public function databaseType(object $notifiable): string
    {
        return 'imports';
    }
}