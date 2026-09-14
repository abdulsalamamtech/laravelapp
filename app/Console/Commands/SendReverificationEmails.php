<?php

namespace App\Console\Commands;

use App\Services\MailService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Description('Dispatches queue jobs for unverified users in chunks of 30 and sends re-verification emails.')]
#[Signature('app:send-reverification-emails')]
class SendReverificationEmails extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(MailService $mailService)
    {
        $mailService->sendBulkReverification();
        $this->info('Reverification batch processed successfully.');
    }
}
