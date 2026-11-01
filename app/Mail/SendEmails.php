<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendEmails extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private string $mailSubject;
    private string $rawMessage;
    private string $userName;

    public function __construct(
        string $subject,
        string $message,
        string $user_name
    ) {
        $this->mailSubject = $subject;
        $this->rawMessage = $message;
        $this->userName = $user_name;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mailSubject,
            bcc: ['cdms.cip@gmail.com'],
        );
    }

    public function content(): Content
    {
Log::info($this->renderMessageWithMentions($this->rawMessage));
        return new Content(
            markdown: 'emails.sendmails',
            with: [
                'url' => url('/'),
                'message' => $this->renderMessageWithMentions(
                    $this->rawMessage
                ),
                'user_name' => $this->userName,
            ],
        );
    }

    /**
     * Safely escape message content and convert valid user
     * mentions into clickable email links.
     *
     * Supported formats:
     *   @Dalitso
     *   @{Dalitso Mandala}
     */

    private function renderMessageWithMentions(string $message): string
    {
        $pattern = '/@\{([^{}\r\n]+)\}|@([A-Za-z0-9_]+)/u';
        preg_match_all($pattern, $message, $matches, PREG_OFFSET_CAPTURE);

        // Collect the exact names referenced in the message.
        $mentionNames = [];
        foreach ($matches[0] as $index => $match) {
            $mention = $match[0];
            $name = str_starts_with($mention, '@{')
                ? substr($mention, 2, -1)
                : substr($mention, 1);
            $mentionNames[] = $name;
        }

        // Retrieve all mentioned users in a single query.
        $users = empty($mentionNames)
            ? collect()
            : User::query()
                ->whereIn('name', array_unique($mentionNames))
                ->get()
                ->keyBy('name');

        $html = '';
        $offset = 0;

        foreach ($matches[0] as $index => $match) {
            [$mentionText, $position] = $match;

            // Append ordinary text before the mention WITHOUT escaping,
            // allowing user-provided HTML to flow through.
            $html .= substr($message, $offset, $position - $offset);

            $name = str_starts_with($mentionText, '@{')
                ? substr($mentionText, 2, -1)
                : substr($mentionText, 1);

            $user = $users->get($name);

            if (
                $user &&
                is_string($user->email) &&
                filter_var($user->email, FILTER_VALIDATE_EMAIL)
            ) {
                // Escape both the URL attribute and visible link text to prevent injection.
                $html .= '<a href="mailto:' . e($user->email) . '">'
                    . e('@' . $user->name)
                    . '</a>';
            } else {
                // Unknown users and invalid email addresses stay plain text.
                $html .= htmlspecialchars($mentionText, ENT_QUOTES, 'UTF-8');
            }

            $offset = $position + strlen($mentionText);
        }

        // Append remaining text without escaping HTML.
        $html .= substr($message, $offset);

        // Optional: If you use Laravel's Markdown mail components,
        // passing raw HTML requires using {!! $message !!} inside your markdown blade template
        // instead of standard {{ $message }}.
        return $html;
    }

    public function attachments(): array
    {
        return [];
    }
}
