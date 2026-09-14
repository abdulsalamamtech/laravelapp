@extends('mail.layouts.mail')

@section('content')
    <!-- Salutation -->
    <h2>{{ $user?->name ? 'Dear ' . $user?->name : 'Hello dear' }},</h2>
    <p>
        You are almost done. Confirm that you accept
        <strong>{{ $pendingEmail }}</strong> as your new login email.
    </p>
    <p>
        Use the code below to accept before
        <strong>{{ $effectiveAt?->format('jS M Y, g:i A') }}</strong>:
    </p>
    <p>
        OTP:
    <div class="button">{{ $otp }}</div>
    <p>
        If you do not accept before this time, the change will be discarded and your
        email will remain unchanged.
    </p>
    </p>
    <p>
        Thank you!
    </p>
@endsection