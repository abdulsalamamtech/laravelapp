@extends('mail.layouts.mail')

@section('content')
    <!-- Salutation -->
    <h2>{{ $user?->name ? 'Dear ' . $user?->name : 'Hello dear' }},</h2>
    <p>
        Your VeriScore account email address has been changed.
    </p>
    <p>
        It was changed from <strong>{{ $oldEmail }}</strong>
        to <strong>{{ $newEmail }}</strong>
        on <strong>{{ now()->format('jS M Y, g:i A') }}</strong>.
    </p>
    <p>
        If you did not expect this change, please secure your account immediately
        and contact support.
    </p>
    <p>
        Thank you!
    </p>
@endsection