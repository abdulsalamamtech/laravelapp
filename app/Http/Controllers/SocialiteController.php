<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SocialiteController extends Controller
{
    /**
     * Supported OAuth providers. To add a new provider:
     * 1. Add env vars (PROVIDER_CLIENT_ID, PROVIDER_CLIENT_SECRET, PROVIDER_REDIRECT_URL)
     * 2. Add config entry in config/services.php
     * 3. Add the provider's ID column to the users table migration
     * 4. Add the column name to this array
     */
    protected const PROVIDERS = [
        'google' => 'google_id',
        'github' => 'github_id',
    ];

    /**
     * Redirect to the OAuth provider.
     */
    public function redirect(string $provider): RedirectResponse
    {
        $this->ensureProviderIsSupported($provider);

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle the OAuth callback from the provider.
     */
    public function callback(string $provider, Request $request): RedirectResponse
    {
        $this->ensureProviderIsSupported($provider);

        try {
            $socialiteUser = Socialite::driver($provider)->user();
        } catch (RuntimeException $runtimeException) {
            Log::error('Socialite callback failed', [
                'provider' => $provider,
                'error' => $runtimeException->getMessage(),
            ]);

            return redirect()->route('login')
                ->with('error', 'Failed to authenticate with '.$provider.'. Please try again.');
        }

        $user = $this->findOrCreateUser($socialiteUser, $provider);

        Auth::login($user, remember: true);

        return redirect()->intended('/administrator');
    }

    /**
     * Find an existing user by provider ID or email, or create a new one.
     */
    protected function findOrCreateUser(SocialiteUser $socialiteUser, string $provider): User
    {
        $column = self::PROVIDERS[$provider];

        // Check if a user already exists with this provider ID
        $user = User::where($column, $socialiteUser->getId())->first();

        if ($user) {
            return $user;
        }

        // Check if a user exists with the same email — link the provider
        $user = User::where('email', $socialiteUser->getEmail())->first();

        if ($user) {
            $user->update([$column => $socialiteUser->getId()]);

            return $user;
        }

        // Create a new user
        return User::create([
            'name' => $socialiteUser->getName() ?? $socialiteUser->getNickname() ?? $socialiteUser->getEmail(),
            'email' => $socialiteUser->getEmail(),
            'password' => Str::random(40),
            $column => $socialiteUser->getId(),
            'avatar' => $socialiteUser->getAvatar(),
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Validate that the provider is supported.
     *
     * @throws NotFoundHttpException
     */
    protected function ensureProviderIsSupported(string $provider): void
    {
        if (! array_key_exists($provider, self::PROVIDERS)) {
            abort(404, 'Unsupported OAuth provider: '.$provider);
        }
    }
}
