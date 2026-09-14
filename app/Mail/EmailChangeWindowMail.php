<?php

namespace App\Mail;

use App\Models\PendingEmailChange;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailChangeWindowMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public User $user, public PendingEmailChange $change, public string $reportOtp) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your email change is scheduled',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.email-change-window.index',
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
        $change = PendingEmailChange::factory()->make([
            'old_email' => $data['old_email'] ?? 'old@example.com',
            'pending_email' => $data['pending_email'] ?? 'new@example.com',
            'effective_at' => now()->addHours(24),
        ]);

        return new static(
            User::factory()->make([
                'name' => $data['name'] ?? 'Jane Doe',
                'email' => $change->old_email,
            ]),
            $change,
            $data['report_otp'] ?? '112233',
        );
    }
}
