<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\MailService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessReverificationEmailJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public User $user)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(MailService $mailService): void
    {
        $mailService->sendSingleReverification($this->user);
    }
}
