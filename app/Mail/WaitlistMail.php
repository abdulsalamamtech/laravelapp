<?php

namespace App\Mail;

use App\Models\Waitlist;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class WaitlistMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Waitlist $waitlist)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $ccAddresses = config('mail.data.cc_addresses');
        // Convert string array into an array of Address objects
        $ccMails = array_map(fn ($email): Address => new Address(trim($email)), $ccAddresses);
        Log::info('Waitlist CC MAil To: ', $ccMails);

        return new Envelope(
            cc: $ccMails,
            subject: '🚀 New Waitlist Lead on VeriScore'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.waitlist.index',
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
            new Waitlist([
                'type' => $data['type'] ?? 'individual',
                'source' => $data['source'] ?? 'website',
                'name' => $data['name'] ?? 'Jane Doe',
                'email' => $data['email'] ?? 'jane@example.com',
                'status' => $data['status'] ?? 'pending',
                'created_at' => now(),
            ]),
        );
    }
}
