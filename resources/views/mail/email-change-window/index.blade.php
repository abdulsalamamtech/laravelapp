@extends('mail.layouts.mail')

@section('content')
    <!-- Salutation -->
    <h2>{{ $user?->name ? 'Dear ' . $user?->name : 'Hello dear' }},</h2>
    <p>
        Your account email is scheduled to change from
        <strong>{{ $change?->old_email }}</strong> to
        <strong>{{ $change?->pending_email }}</strong>.
    </p>
    <p>
        The change takes effect on
        <strong>{{ $change?->effective_at?->format('jS M Y, g:i A') }}</strong>.
    </p>
    <p>
        If you did <strong>not</strong> approve this change, you can stop it and keep
        your existing email by reporting suspicious activity using the code below:
    </p>
    <p>
        Code:
    <div class="button">{{ $reportOtp }}</div>
    <p>
        Enter it at <strong>email/change/report</strong> on the platform before the change
        takes effect. Your current email will be retained and your account secured.
    </p>
    </p>
    <p>
        Thank you!
    </p>
@endsection