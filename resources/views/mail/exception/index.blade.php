@extends('mail.layouts.mail')

@section('content')
    <!-- Salutation -->
    <h2>Hi Engineer,</h2>
    <p>An error occurred while processing a request.</p>

    <div>
        <h3>🚨 System Exception Report</h3>

        <!-- Basic Details -->
        <p><strong>Error Message:</strong> {{ $e->getMessage() ?: 'Nill' }}</p>
        <p><strong>Exception Class:</strong> {{ get_class($e) }}</p>
        <p><strong>Error Code:</strong> {{ $e->getCode() ?: 'Nill' }}</p>
        </hr>


        <!-- HTTP Status Code -->
        <p><strong>HTTP Status Code:</strong>
            {{ method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (property_exists($e, 'status') ? $e->status : 'Nill') }}
        </p>

        <!-- File Location -->
        <p><strong>File Path:</strong> {{ $e->getFile() ?: 'Nill' }}</p>
        <p><strong>Line Number:</strong> {{ $e->getLine() ?: 'Nill' }}</p>

        <!-- Validation Errors (Only displays if it is a validation exception) -->
        @if (method_exists($e, 'errors'))
            <div>
                <strong>Validation Errors:</strong>
                <pre>{{ json_encode($e->errors(), JSON_PRETTY_PRINT) }}</pre>
            </div>
        @endif

        <!-- Contextual Data (Only displays if Laravel has custom context data) -->
        @if (method_exists($e, 'context'))
            <div>
                <strong>Contextual Data:</strong>
                <pre>{{ json_encode($e->context(), JSON_PRETTY_PRINT) }}</pre>
            </div>
        @endif

        <!-- Previous Exception Tracker -->
        @if ($e->getPrevious())
            <p><strong>Previous Exception:</strong> {{ $e->getPrevious()->getMessage() }}</p>
        @endif
        </hr>

        <!-- Full Stack Trace String -->
        <div>
            <strong>Stack Trace:</strong>
            <pre style="background: #f4f4f4; padding: 10px; overflow-x: auto;">{{ Str::of($e->getTraceAsString())->limit(1000) }}</pre>
        </div>
    </div>
    </hr>

    <div>
        <p> <strong>Ip Address:</strong> {{ request()?->ip() ?? 'Nill' }}</p>
        <p> <strong>URL:</strong> {{ config('app.url') ?? 'Nill' }}</p>
        <p> <strong>URL Path:</strong> {{ request()?->fullUrl() ?? 'Nill' }}</p>
        <p> <strong>Guest:</strong> {{ request()?->user()?->email ?? (auth('sanctum')->user()?->email ?? 'Nill') }}</p>
        <p> <strong>User:</strong> {{ auth('sanctum')->user()?->email ?? 'Nill' }}</p>
        <p> <strong>Date:</strong> {{ \Carbon\Carbon::parse(now())->format('jS M Y, g:i A') ?? 'Nill' }}</p>
        <p> <strong>Since:</strong> {{ \Carbon\Carbon::parse(now())->diffForHumans() ?? 'Nill' }}</p>
    </div>
    <div>
        <small>This alert is throttled. You will not receive another notification for this error type for 10
            minutes.</small>
    </div>

    <p>
        Please look into it. <br>
        Thank you!
    </p>
@endsection
