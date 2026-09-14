<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ReverifyEmailMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public User $user, public string $otp)
    {
        Log::info('Reverify Email Mail - initiate class');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Unblock your VeriScore dashboard, '.($this?->user?->name ?? $this?->user?->email),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.reverify-email.index',
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
                'email' => $data['email'] ?? 'jane@example.com',
            ]),
            $data['otp'] ?? '123456',
        );
    }
}
