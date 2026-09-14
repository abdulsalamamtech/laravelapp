@extends('mail.layouts.mail')

@section('content')
    <!-- Salutation -->
    <h2>Hi Admin,</h2>
    <p>A new message has been submitted via the website contact form. Here are the details:</p>

    <div>
        <p> <strong>Subject:</strong> {{ $contact['subject'] ?? 'Nill' }}</p>
        <p> <strong>Name:</strong> {{ ($contact['first_name'] . ' ' . $contact['last_name']??'' . ' ' . $contact['other_name']??'' )?? 'Nill' }}</p>
        <p> <strong>Email:</strong> {{ $contact['email'] ?? 'Nill' }}</p>
        <p> <strong>Phone:</strong> {{ $contact['phone_number'] ?? 'Nill' }}</p>
        <p> <strong>Purpose:</strong> {{ $contact['purpose'] ?? 'Nill' }}</p>
        <p> <strong>Organization:</strong> {{ $contact['organization'] ?? 'Nill' }}</p>
        <p> <strong>Message:</strong> <br>
            {{ $contact['message'] ?? 'Nill' }}
        </p>
        <p> <strong>Date:</strong>
            {{ \Carbon\Carbon::parse($contact['created_at'] ?? now())->format('jS M Y, g:i A') ?? 'Nill' }}</p>
        <p> <strong>Since:</strong> {{ \Carbon\Carbon::parse($contact['created_at'] ?? now())->diffForHumans() ?? 'Nill' }}
        </p>
    </div>

    <p>
        Please respond to the user at their provided email address. <br>
        Thank you!
    </p>
@endsection
