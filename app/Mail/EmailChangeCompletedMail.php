<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailChangeCompletedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public User $user, public string $oldEmail, public string $newEmail) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your email address was changed',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.email-change-completed.index',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Build a sample instance for the mail preview route.
     */
    public static function preview(array $data = []): static
    {
        return new static(
            User::factory()->make([
                'name' => $data['name'] ?? 'Jane Doe',
                'email' => $data['new_email'] ?? 'new@example.com',
            ]),
            $data['old_email'] ?? 'old@example.com',
            $data['new_email'] ?? 'new@example.com',
        );
    }
}
