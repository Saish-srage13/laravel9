<?php

namespace App\Support;

use App\Enum\EmailDeliveryStatus;
use App\Indices\EmailLog;
use App\Jobs\HandleEmailDelivery;
use App\Mail\SendEmail;
use App\Utilities\Helper\RedisHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PendingEmail
{
    public string $id;

    public string $toEmail;

    public string $subject;

    public string $body;

    public EmailDeliveryStatus $status;

    /**
     * Create a pending email object to represent email that are not delivered yet
     *
     * @param string $toEmail
     * @param string $subject
     * @param string $body
     * @return PendingEmail
     */
    public static function createFromRequest($toEmail, $subject, $body)
    {
        $pendingEmail = new static();
        $pendingEmail->id = Str::uuid();
        $pendingEmail->toEmail = $toEmail;
        $pendingEmail->subject = $subject;
        $pendingEmail->body = $body;
        $pendingEmail->status = EmailDeliveryStatus::PENDING;

        return $pendingEmail;
    }

    public static function createFromElasticResponse($data)
    {
        $pendingEmail = new static();
        $pendingEmail->id = $data['id'];
        $pendingEmail->toEmail = $data['toEmail'];
        $pendingEmail->subject = $data['subject'];
        $pendingEmail->body = $data['body'];
        $pendingEmail->status = EmailDeliveryStatus::fromValue($data['status']);

        return $pendingEmail;
    }

    /**
     * Log the message persistent store
     *
     * @param  array $config ['redis', 'elastic_search']
     * @return PendingEmail
     */
    public function log(array $config = ['redis', 'elastic_search'])
    {
        if (in_array('redis', $config)) {
            (new RedisHelper)->store(id: $this->id, data: collect($this)->toArray());
        }

        if (in_array('elastic_search', $config)) {
            $id = (new EmailLog)->store(data: $this);
        }

        return $this;
    }

    /**
     * Send email
     * 
     * @return PendingEmail
     */
    public function send()
    {
        Log::info($this->id);
        
        $this->status = EmailDeliveryStatus::QUEUED;
        (new EmailLog)->store(data: $this);

        // A delay is being added for testing purposes

        dispatch(new HandleEmailDelivery($this))->delay(now()->addSeconds(2));

        /*

            Instead of using a job, the mail class can be queued


            Mail::to($this->toEmail)
                ->later(Carbon::now()->addSeconds(2), new SendEmail($this));
                Can be normally queued or queued after a certain amount of time
                ->queue(new SendEmail($this));

        */

    }

    public function markAsSent()
    {
        $this->status = EmailDeliveryStatus::SENT;
    }
}