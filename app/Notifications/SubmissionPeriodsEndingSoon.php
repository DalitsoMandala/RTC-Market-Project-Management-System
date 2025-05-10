<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class SubmissionPeriodsEndingSoon extends Notification implements ShouldQueue
{
    use Queueable;

    protected $forms;

    /**
     * Create a new notification instance.
     *
     * @param array $forms Grouped forms and periods for the user
     */
    public function __construct(array $forms)
    {
        $this->forms = $forms;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject('Forms with Submission Periods Ending Soon')
            ->greeting("Hello {$notifiable->name},")
            ->line('The following forms you are responsible for have submission periods ending soon:');

        foreach ($this->forms as $form) {
            $mail->line("**Form:** {$form['form_name']}");
            foreach ($form['periods'] as $period) {
                $mail->line("- Indicator: {$period['indicator_name']}");
                $mail->line(new HtmlString("Ending Date: <span style='color:red; font-weight: bold'>{$period['end']}</span>"));
            }
            $mail->line(''); // blank line for spacing
        }

        $mail->line('Please ensure your submissions are completed in time.');

        return $mail;
    }
}
