<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as ProviderUser;

function fakeProviderUser(array $attributes = []): SocialiteUser
{
    return tap(new ProviderUser, function (ProviderUser $user) use ($attributes) {
        $user->map(array_merge([
            'id' => 'oauth-id-123',
            'name' => 'OAuth User',
            'nickname' => 'oauthuser',
            'email' => 'oauth@example.com',
            'avatar' => 'https://example.com/avatar.png',
        ], $attributes));
    });
}

describe('SocialiteController', function () {
    it('redirects the user to the provider authorization page', function () {
        Socialite::fake('google');

        $this->get('/auth/google')
            ->assertRedirect('https://socialite.fake/google/authorize');
    });

    it('returns 404 for an unsupported provider', function () {
        $this->get('/auth/twitter')
            ->assertStatus(404);

        $this->get('/auth/twitter/callback')
            ->assertStatus(404);
    });

    it('creates a new user on first login via a provider', function () {
        Socialite::fake('github', fakeProviderUser([
            'id' => 'gh-123',
            'email' => 'new@example.com',
        ]));

        $this->get('/auth/github/callback')
            ->assertRedirect('/administrator')
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', [
            'email' => 'new@example.com',
            'name' => 'OAuth User',
            'github_id' => 'gh-123',
            'avatar' => 'https://example.com/avatar.png',
        ]);

        $this->assertAuthenticated();
        $this->assertSame('new@example.com', Auth::user()->email);
    });

    it('does not overwrite a user that already has the provider linked', function () {
        User::factory()->create([
            'email' => 'existing@example.com',
            'name' => 'Existing Name',
            'google_id' => 'google-456',
        ]);

        Socialite::fake('google', fakeProviderUser([
            'id' => 'google-456',
            'name' => 'Incoming Name',
            'email' => 'existing@example.com',
        ]));

        $this->get('/auth/google/callback')
            ->assertRedirect('/administrator');

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'email' => 'existing@example.com',
            'name' => 'Existing Name',
            'google_id' => 'google-456',
        ]);
        $this->assertAuthenticatedAs(User::where('email', 'existing@example.com')->first());
    });

    it('links the provider to an existing account with the same email', function () {
        $user = User::factory()->create([
            'email' => 'match@example.com',
            'name' => 'Existing User',
        ]);

        Socialite::fake('google', fakeProviderUser([
            'id' => 'google-789',
            'email' => 'match@example.com',
        ]));

        $this->get('/auth/google/callback')
            ->assertRedirect('/administrator');

        $this->assertDatabaseHas('users', [
            'email' => 'match@example.com',
            'name' => 'Existing User',
            'google_id' => 'google-789',
        ]);
        $this->assertSame($user->id, Auth::id());
    });

    it('redirects back to login when the provider returns an error', function () {
        Socialite::fake('google', fn (): SocialiteUser => throw new RuntimeException('Access denied'));

        $this->get('/auth/google/callback')
            ->assertRedirect(route('login'))
            ->assertSessionHas('error');
    });
});
