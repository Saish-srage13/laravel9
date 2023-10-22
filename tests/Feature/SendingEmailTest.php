<?php

namespace Tests\Feature;

use App\Jobs\HandleEmailDelivery;
use App\Mail\SendEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SendingEmailTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_application_throws_a_validation_error_and_does_not_queue_mail_if_the_request_object_is_incorrect_or_malformed()
    {
        Mail::fake();

        Queue::fake();

        $response = $this->post('/api/token/send', [
            "emails" => [
                [
                    "to_email" => "testemail1@email.com",
                    "subject" => "Subject 1",
                    "body" => "Body 1"
                ],
                [
                    "subject" => "Subject 2",
                    "body" => "Body 2"
                ]
            ]
        ]);

        $response
            ->assertStatus(400)
            ->assertJson([
                "status" => false,
                "message" => "To email is required",
                "data" => [
                    "errors" => [
                        "To email is required"
                    ]
                ]
            ]);

        Queue::assertNotPushed(HandleEmailDelivery::class);

        /*
            These to assets were incase the Mailable class was directly being queued instead of have a job 

            Mail::assertNotQueued(SendEmail::class);

        */
    }

    public function test_mail_is_queued_up_if_request_body_is_valid()
    {
        Mail::fake();

        Queue::fake();

        $response = $this->post('/api/token/send', [
            "emails" => [
                [
                    "to_email" => "testemail1@email.com",
                    "subject" => "Subject 1",
                    "body" => "Body 1"
                ],
                [
                    "to_email" => "testemail2@email.com",
                    "subject" => "Subject 2",
                    "body" => "Body 2"
                ]
            ]
        ]);

        $response->assertStatus(200)
            ->assertJson([
                "status" => true,
                "message" => "Email's queued successfully!",
                "data" => []
            ]);

        Queue::assertPushed(HandleEmailDelivery::class);
        
        /*
            These to assets were incase the Mailable class was directly being queued instead of have a job 

            Mail::assertQueued(SendEmail::class);

            Mail::assertQueuedCount(2);
        
        */
    }

}
