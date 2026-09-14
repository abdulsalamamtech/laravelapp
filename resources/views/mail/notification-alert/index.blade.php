@extends('mail.layouts.mail')

@section('content')
    <!-- Salutation -->
    <h1>{{ $user?->name ? 'Dear ' . $user?->name : 'Hello dear' }},</h1>
    <h2> {{ $data['title'] }}</h2>
    <div>{!! $data['message'] !!}</div>
    @if (!empty($data) && !empty($data['content']))
        <div style="margin: 10px auto; padding: 8px; background: rgb(205, 234, 250);">{!! $data['content'] !!}</div>
    @endif
    <p>To view your business metrics, please click the link below:</p>
    <div>
        <a href="{{ config('app.frontend_url') ?? 'https://veriscore.app' }}/dashboard" target="_blank"
            rel="noopener noreferrer">
            {{ config('app.frontend_url') ?? 'https://veriscore.app/dashboard' }}/dashboard
        </a>
    </div>
    <p>Thank you!</p>
@endsection
