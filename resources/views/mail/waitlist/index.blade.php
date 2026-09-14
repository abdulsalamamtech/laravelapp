@extends('mail.layouts.mail')

@section('content')
    <!-- Salutation -->
    <h2>Hi Admin,</h2>
    <p>A new user has just joined the waitlist. Here are their details:</p>

    <div>
        {{-- <p> <strong>Name:</strong> {{ $waitlist['name'] ?? 'Nill' }}</p> --}}
        <p> <strong>Email:</strong> {{ $waitlist['email'] ?? 'Nill' }}</p>
        <p> <strong>Source:</strong> {{ $waitlist['source'] ?? 'Nill' }}</p>
        <p> <strong>Date:</strong>
            {{ \Carbon\Carbon::parse($waitlist['created_at'] ?? now())->format('jS M Y, g:i A') ?? 'Nill' }}</p>
        <p> <strong>Since:</strong> {{ \Carbon\Carbon::parse($waitlist['created_at'] ?? now())->diffForHumans() ?? 'Nill' }}
        </p>
    </div>

    <p>
        Thank you!
    </p>
@endsection
