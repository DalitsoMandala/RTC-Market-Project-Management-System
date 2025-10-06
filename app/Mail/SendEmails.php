<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendEmails extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

 public $subject;
    public $message;
    public $user_name;
    /**
     * Create a new notification instance.
     */
    public function __construct($subject, $message, $user_name)
    {
        //
        $this->subject = $subject;
        $this->message = $message;
        $this->user_name = $user_name;

         if (str_contains($this->message, '_email_name_')) {
            $this->message = str_replace('_email_name_', $this->user_name, $this->message);
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,

            bcc: [
                'cdms.cip@gmail.com'
            ]
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.sendmails',

        with: [

            'url' => url('/'),

        ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
