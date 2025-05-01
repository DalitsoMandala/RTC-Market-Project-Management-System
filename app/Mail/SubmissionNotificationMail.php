<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubmissionNotificationMail extends Mailable
{
    use Queueable, SerializesModels;
    public $submissionPeriod;
    public $reminderType;
    public $user;
    /**
     * Create a new message instance.
     */
    public function __construct(array $submissionPeriod, string $reminderType, $user)
    {
        $this->submissionPeriod = $submissionPeriod;
        $this->reminderType = $reminderType;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Submission Expired',
            replyTo: [
              env('MAIL_FROM_ADDRESS')
            ]
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.submission_expired',
            with: [
                'submissionPeriod' => $this->submissionPeriod,
                'reminderType' => $this->reminderType,

            ]
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
