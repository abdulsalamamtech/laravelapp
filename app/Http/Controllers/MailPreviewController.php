<?php

namespace App\Http\Controllers;

use App\Mail\AlertUserMail;
use App\Mail\CompanyWelcomeMail;
use App\Mail\ContactMail;
use App\Mail\EmailChangeAcceptanceMail;
use App\Mail\EmailChangeCompletedMail;
use App\Mail\EmailChangeConfirmationMail;
use App\Mail\EmailChangeRequestMail;
use App\Mail\EmailChangeWindowMail;
use App\Mail\ExceptionMail;
use App\Mail\ForgetPasswordMail;
use App\Mail\ReverifyEmailMail;
use App\Mail\TwoFactorAuthMail;
use App\Mail\VerifyAccountMail;
use App\Mail\WaitlistMail;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;

class MailPreviewController extends Controller
{
    /**
     * Available mail previews, keyed by route slug.
     */
    protected array $mails = [
        'verify-account' => VerifyAccountMail::class,
        'forget-password' => ForgetPasswordMail::class,
        'two-factor-auth' => TwoFactorAuthMail::class,
        'reverify-email' => ReverifyEmailMail::class,
        'notification-alert' => AlertUserMail::class,
        'company-welcome' => CompanyWelcomeMail::class,
        'contact' => ContactMail::class,
        'exception' => ExceptionMail::class,
        'waitlist' => WaitlistMail::class,
        'email-change-request' => EmailChangeRequestMail::class,
        'email-change-confirmation' => EmailChangeConfirmationMail::class,
        'email-change-window' => EmailChangeWindowMail::class,
        'email-change-acceptance' => EmailChangeAcceptanceMail::class,
        'email-change-completed' => EmailChangeCompletedMail::class,
    ];

    /**
     * List all available previews.
     */
    public function index()
    {
        $slugs = array_keys($this->mails);
        $html = '<h1>Mail previews</h1><ul>';
        foreach ($slugs as $slug) {
            $html .= '<li><a href="'.route('mail.show', ['slug' => $slug]).'">'.$slug.'</a></li>';
        }

        $html .= '</ul>';

        return response($html)->header('Content-Type', 'text/html');
    }

    /**
     * Render a single mailable with query-string custom data.
     */
    public function show(Request $request, string $slug)
    {
        if (! array_key_exists($slug, $this->mails)) {
            abort(404, 'No such mail preview: '.$slug);
        }

        $class = $this->mails[$slug];
        $data = $request->all();

        Log::debug('MailPreview: rendering '.$slug, $data);

        /** @var Mailable $mailable */
        $mailable = $class::preview($data);

        return response($mailable->render())->header('Content-Type', 'text/html');
    }
}
