<?php

use Illuminate\Support\Facades\Cache;

// The preview routes are gated by the env; make sure they exist in the test env.
beforeEach(function () {
    Cache::flush();
});

describe('Mail preview', function () {
    it('lists every available preview on /mail', function () {
        $this->get('/mail')
            ->assertStatus(200)
            ->assertSee('verify-account')
            ->assertSee('email-change-request')
            ->assertSee('email-change-completed');
    });

    it('renders a mailable on its slug page with default data', function () {
        $this->get('/mail/email-change-request')
            ->assertStatus(200)
            ->assertSee('new@example.com')
            ->assertSee('123456');
    });

    it('honours query-string data overrides', function () {
        $this->get('/mail/email-change-request?pending_email=custom@example.com')
            ->assertStatus(200)
            ->assertSee('custom@example.com')
            ->assertDontSee('new@example.com');
    });

    it('renders the two-factor mail without errors', function () {
        $this->get('/mail/two-factor-auth')
            ->assertStatus(200)
            ->assertSee('123456');
    });

    it('returns 404 for an unknown slug', function () {
        $this->get('/mail/does-not-exist')
            ->assertStatus(404);
    });
});
