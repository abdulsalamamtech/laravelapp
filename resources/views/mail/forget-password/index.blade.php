@extends('mail.layouts.mail')

@section('content')
    <!-- Salutation -->
    <h2>{{ $user?->name ? 'Dear ' . $user?->name : 'Hello dear' }},</h2>
    <p>
        You requested for an OTP because you forgot your password.
        Use the below OTP to setup a new password.
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
