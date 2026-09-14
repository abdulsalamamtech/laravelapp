@extends('mail.layouts.mail')

@section('content')
    <!-- Salutation -->
    <h2>{{ $user?->name ? 'Dear ' . $user?->name : 'Hello dear' }},</h2>
    <p>
        Someone is trying to sign in to your VeriScore account.
        Use the below OTP to complete your two-factor authentication.
    </p>
    <p>
        OTP:
    <div class="button">{{ $otp }}</div>
    <p>
        If you didn't request for it ignore this email and no action will take place.
    </p>
    </p>
    <p>
        Thank you!
    </p>
@endsection