<?php

namespace Database\Factories;

use App\Enums\EmailChangeStatus;
use App\Enums\EmailChangeWindow;
use App\Models\PendingEmailChange;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PendingEmailChange>
 */
class PendingEmailChangeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => fake()->uuid(),
            'old_email' => fake()->safeEmail(),
            'pending_email' => fake()->unique()->safeEmail(),
            'status' => EmailChangeStatus::PENDING,
            'initiated_at' => now(),
            'effective_at' => now()->addMinutes(EmailChangeWindow::TWENTY_FOUR_HOURS->toMinutes()),
            'ip_address' => '127.0.0.1',
        ];
    }

    /**
     * Bind to a real user and use their current email as the old address.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (): array => [
            'user_id' => $user->id,
            'old_email' => $user->email,
        ]);
    }

    /**
     * Both inbox confirmations completed; waiting on the new-inbox acceptance.
     */
    public function scheduled(): static
    {
        return $this->state(fn (): array => [
            'status' => EmailChangeStatus::SCHEDULED,
            'old_confirmed_at' => now(),
            'new_confirmed_at' => now(),
        ]);
    }

    /**
     * New inbox accepted; applying once the window closes.
     */
    public function accepted(): static
    {
        return $this->scheduled()->state(fn (): array => [
            'accepted_at' => now(),
        ]);
    }

    /**
     * The scheduled change has already been applied.
     */
    public function applied(): static
    {
        return $this->accepted()->state(fn (): array => [
            'status' => EmailChangeStatus::APPLIED,
            'changed_at' => now(),
            'effective_at' => now()->subMinute(),
        ]);
    }
}
