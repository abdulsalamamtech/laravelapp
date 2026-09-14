@extends('mail.layouts.mail')

@section('content')
    <!-- Salutation -->
    <h2>{{ $user?->name ? 'Dear ' . $user?->name : 'Hello dear' }},</h2>
    <p>
        A request was made to change the email address on your account
        to <strong>{{ $pendingEmail }}</strong>.
    </p>
    <p>
        If this was you, use the code below to approve the change:
    </p>
    <p>
        OTP:
    <div class="button">{{ $otp }}</div>
    <p>
        If you did <strong>not</strong> initiate this request, ignore this email.
        Nothing will change until the new email address is also confirmed from its inbox.
    </p>
    </p>
    <p>
        Thank you!
    </p>
@endsection