<?php

namespace App\Http\Controllers;

use App\Enum\EmailDeliveryStatus;
use App\Http\Requests\EmailRequest;
use App\Indices\EmailLog;
use App\Support\PendingEmail;
use App\Utilities\Contracts\ElasticsearchHelperInterface;
use App\Utilities\Contracts\RedisHelperInterface;
use Elasticsearch;

class EmailController extends ApiController
{
    // TODO: finish implementing send method
    public function send($userToken, EmailRequest $validatedEmails)
    {
        collect($validatedEmails['emails'])->each(function ($emailRequest) {
            PendingEmail::createFromRequest(
                toEmail: $emailRequest['to_email'],
                subject: $emailRequest['subject'],
                body: $emailRequest['body']
            )
            ->log()
            ->send();
        });

        return $this->respondOk(message: 'Email\'s queued successfully!');
    }

    //  TODO - BONUS: implement list method
    public function list()
    {
        $emails = (new EmailLog)->getList();

        return $this->respondOk($emails);
    }
}
