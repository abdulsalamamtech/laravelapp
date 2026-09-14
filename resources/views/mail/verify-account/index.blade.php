@extends('mail.layouts.mail')

@section('content')
    <!-- Salutation -->
    <h2>Dear {{ $user?->name }},</h2>
    <p>
        You are welcome to VeriScore.
        Please verify your account.
    </p>
    <p>
        OTP:
    <div class="button">{{ $otp }}</div>

    <p>
        If you can't copy the code, copy and paste this link into your browser:
        <a href="{{ config('app.frontend_url') }}/verifyOtp?email={{ $user?->email ?? '' }}&otp={{ $otp ?? '' }}&date={{ now() }}"
            target="_blank" class="cta-btn"
            style="
            display: inline-block;
            padding: 14px 18px;
            font-family:
                &quot;Work Sans&quot;,
                sans-serif;
            font-size: 16px;
            font-weight: 600;
            color: #0766AD; 
            text-decoration: none;
            border-radius: 8px;
        ">  
            {{ config('app.frontend_url') }}/verifyOtp?email={{ $user?->email ?? '' }}&otp={{ $otp ?? '' }}&date={{ now() }}&token={{ md5(random_bytes(4)) }}
        </a>
    </p>
    </p>
    <p>
        This OTP code expires in 10 minutes.
    </p>
    <p>
        You are welcome on board.
    </p>
    <p>
        Thank you!
    </p>
@endsection
