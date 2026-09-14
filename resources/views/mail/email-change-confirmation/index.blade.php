@extends('mail.layouts.mail')

@section('content')
    <!-- Salutation -->
    <h2>{{ $user?->name ? 'Dear ' . $user?->name : 'Hello dear' }},</h2>
    <p>
        You requested to use <strong>{{ $pendingEmail }}</strong> as the email
        address on your VeriScore account.
    </p>
    <p>
        Use the code below to confirm that you own this inbox:
    </p>
    <p>
        OTP:
    <div class="button">{{ $otp }}</div>
    <p>
        After this address is confirmed and your current inbox approves,
        the change is scheduled and you will receive further instructions.
    </p>
    </p>
    <p>
        Thank you!
    </p>
@endsection