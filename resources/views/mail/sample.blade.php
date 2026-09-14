{{-- 

<div>
    <!-- Simplicity is the ultimate sophistication. - Leonardo da Vinci -->
</div> 

--}}

@extends('mail.layouts.mail')

@section('content')
    <!-- Salutation -->
    <h2>Dear {{ $user?->name }},</h2>
    <p>
        Thanks for submitting your request! for
        {{ $account?->request?->title ?? '' }}
        {{ $account?->request?->type ?? '' }}
        for this month.
    </p>
    <p>
        We want to let you know that we've received it and will be reviewing it within 3 working days.
    </p>
    <p>
        You'll get another email from us once the review is complete.
        In the meantime, you can always check the status of
        your request on your
        <a href="http://domain.org/auth/login" target="_blank" rel="noopener noreferrer">Domain.</a>
    </p>
    <p>
        Thanks for your patience!
    </p>
@endsection
