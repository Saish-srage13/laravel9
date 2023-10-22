<?php

namespace App\Listeners;

use App\Enum\EmailDeliveryStatus;
use App\Indices\EmailLog;
use App\Support\PendingEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class EmailSentListner
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MessageSent $event): void
    {        
        $emailLog = (new EmailLog)->getById(['id' => $event->data['pendingEmail']->id]);

        if (!empty($emailLog)) {
            $pendingEmail = PendingEmail::createFromElasticResponse($emailLog);
            $pendingEmail->status = EmailDeliveryStatus::SENT;
            (new EmailLog)->store($pendingEmail);
        }
    }
}
