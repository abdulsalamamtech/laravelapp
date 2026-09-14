<?php

namespace App\Mail;

use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailChangeAcceptanceMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public User $user, public string $pendingEmail, public string $otp, public ?CarbonInterface $effectiveAt = null) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Accept your new email address',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.email-change-acceptance.index',
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
                'email' => $data['old_email'] ?? 'old@example.com',
            ]),
            $data['pending_email'] ?? 'new@example.com',
            $data['otp'] ?? '445566',
            now()->addHours(24),
        );
    }
}
