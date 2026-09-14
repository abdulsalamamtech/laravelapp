<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ContactMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Contact $contact)
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
        Log::info('Contact CC MAil To: ', $ccMails);

        return new Envelope(
            cc: $ccMails,
            subject: '✉️ New Contact Message - '.($this->contact?->subject ?? 'support')
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.contact.index',
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
            new Contact([
                'first_name' => $data['first_name'] ?? 'Jane',
                'last_name' => $data['last_name'] ?? 'Doe',
                'other_name' => $data['other_name'] ?? '',
                'email' => $data['email'] ?? 'jane@example.com',
                'phone_number' => $data['phone_number'] ?? '+234 800 000 0000',
                'subject' => $data['subject'] ?? 'Sample enquiry',
                'purpose' => $data['purpose'] ?? 'General support',
                'organization' => $data['organization'] ?? 'Northwind Ltd',
                'message' => $data['message'] ?? 'I would like to learn more about VeriScore.',
                'created_at' => now(),
            ]),
        );
    }
}
