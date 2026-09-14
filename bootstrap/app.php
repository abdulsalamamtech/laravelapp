<?php

use App\Exceptions\ProFeatureAccessException;
use App\Mail\ExceptionMail;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Encryption\MissingAppKeyException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Http\Request;
use Illuminate\Queue\MaxAttemptsExceededException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Symfony\Component\ErrorHandler\Error\FatalError;
use Symfony\Component\ErrorHandler\Error\OutOfMemoryError;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // Start of customized middleware
        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
            // Api Exception
            // \App\Http\Middleware\ApiExceptionMiddleware::class,
        ]);

        // Exclude csrf comment this for security on production
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);

        // Register cors middleware
        $middleware->prepend(HandleCors::class);

        // Custom middleware
        $middleware->alias([
            // 'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            // This is for laravel spatie/laravel-permission
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);

        // Incoming requests from your SPA can authenticate using Laravel's session cookies
        $middleware->statefulApi();
        // End of customized middleware
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // Stop logging - To prevent Laravel from logging or sending your ProFeatureAccessException to error trackers
        $exceptions->dontReport([
            ProFeatureAccessException::class,
        ]);

        // Start of render customized error message
        $exceptions->render(function (Throwable $e, Request $request) {

            // Log::info('Request:', $request->all() ?? $request->getContent());
            // Log::info('Raw Input: ' . $request->getContent());
            Log::error('Error:', [$e?->getMessage(), $e?->getTraceAsString()]);

            // Working with API requests
            if ($request->is('api/*')) {

                // Custom response for all exceptions
                $response = [
                    'success' => false,
                    'message' => 'An error occurred. Please try again later.',
                    // Avoid exposing error details in production
                    'error' => config('app.debug') ? $e->getMessage() : 'Internal Server Error',
                ];

                // Set a default status code
                $statusCode = 500;

                // Customize response for different exception types
                if ($e instanceof ModelNotFoundException) {
                    $response['message'] = 'Resource not found.';
                    $statusCode = 404;
                } elseif ($e instanceof NotFoundHttpException) {
                    $response['message'] = 'Endpoint not found.';
                    $statusCode = 404;
                } elseif ($e instanceof AuthenticationException) {
                    $response['message'] = 'Unauthenticated.';
                    $statusCode = 401;
                } elseif ($e instanceof AuthorizationException) {
                    $response['message'] = 'Unauthorized.';
                    $statusCode = 403;
                } elseif ($e instanceof ValidationException) {
                    $response['message'] = 'Validation failed.';
                    $response['errors'] = $e->errors();
                    $statusCode = 422;
                } elseif ($e instanceof HttpExceptionInterface) {
                    $statusCode = $e->getStatusCode();
                }

                // Check if the request expects a JSON response
                if ($request->expectsJson()) {
                    return response()->json($response, $statusCode);
                }

                return response()->json($response, $statusCode);
            }
        });
        // End of render customized error message

        // Start of Sending Exception Mail
        $exceptions->report(function (Throwable $e) {

            $criticalClasses = [
                // --- Runtime & Memory ---
                FatalError::class,
                OutOfMemoryError::class,
                Error::class,
                TypeError::class,                                            // Added: Raw PHP strict type errors
                Throwable::class,                                            // Added: Universal base interface for all errors

                // --- Data & Storage ---
                QueryException::class,
                FileNotFoundException::class,

                // --- Security & Core ---
                DecryptException::class,
                MissingAppKeyException::class,

                // --- Infrastructure & Queues ---
                \class_exists('\RedisException') ? RedisException::class : Exception::class,
                MaxAttemptsExceededException::class,
                ConnectException::class,
            ];

            // 1. Check if the error matches or extends any critical class or Throwable type
            $isCriticalClass = collect($criticalClasses)->contains(fn ($class) => $e instanceof $class);

            // 2. Safely capture 500-level HTTP server exceptions
            $isServerError = method_exists($e, 'getStatusCode') && $e->getStatusCode() >= 500;

            // send exception email only if the error is critical or a server error and MAIL_SEND_EXCEPTIONS is true
            if (! config('mail.data.send_exceptions', false)) {
                Log::info('Exception email sending is disabled in configuration.');

                return; // Exit early if exception emails are disabled
            }

            if ($isCriticalClass || $isServerError) {
                // $cacheKey = 'critical_exception_mail';
                $cacheKey = 'exception_'.md5($e->getFile().'_'.$e->getLine());

                if (! Cache::has($cacheKey)) {
                    // Use a try-catch so mailer failures don't crash the error handler itself
                    try {
                        $devMail = config('mail.data.dev');
                        Mail::mailer('dev_smtp')->to($devMail)->send(new ExceptionMail($e));
                        Cache::put($cacheKey, true, now()->addMinutes(1));
                    } catch (Throwable $mailError) {
                        Log::alert('Mail not sent after exception');
                        // Log if the mail system itself is down or misconfigured
                        logger()->error('Failed sending exception email: '.$mailError->getMessage());
                    }
                } else {
                    Log::alert('Mail not sent after exception because this error was cache: ', [$e]);
                }
            }
        });
        // End of Sending Exception Mail

    })->create();
