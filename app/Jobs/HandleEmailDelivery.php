<?php

namespace App\Jobs;

use App\Mail\SendEmail;
use App\Support\PendingEmail;
use Illuminate\Support\Facades\Mail;

class HandleEmailDelivery extends Job
{

    public PendingEmail $pendingEmail;

    /**
     * Create a new job instance.
     */
    public function __construct(PendingEmail $pendingEmail)
    {
        $this->pendingEmail = $pendingEmail;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->pendingEmail->toEmail)
            ->send(new SendEmail($this->pendingEmail));
    }
}
