<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ExceptionMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Throwable $e)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $devCcAddresses = config('mail.data.dev_cc_addresses');
        // Convert string array into an array of Address objects
        $ccMails = array_map(fn ($email): Address => new Address(trim($email)), $devCcAddresses);
        Log::info('Exception CC MAil To: ', $ccMails);

        $appUrl = config('app.url');

        $errMessage = Str::of($this->e->getMessage())->limit(300);

        return new Envelope(
            cc: $ccMails,
            subject: '⚠️ A critical error occurred on the website: ('.$appUrl.') 🔥 - '.$errMessage
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        Log::info('Sending Exception Mail', [$this->e]);

        return new Content(
            view: 'mail.exception.index',
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
            new \RuntimeException($data['message'] ?? 'Sample exception message for the mail preview.', 500),
        );
    }
}
