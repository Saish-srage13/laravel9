<?php

namespace Tests\Feature;

use App\Indices\EmailLog;
use App\Jobs\HandleEmailDelivery;
use App\Mail\SendEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RetrievingEmailListTest extends TestCase
{
    public function test_get_list_api_returns_email_list()
    {
        $response = $this->post('/api/token/send', [
            'emails' => [
                [
                    'to_email' => 'testemail101@email.com',
                    'subject' => 'Subject 101',
                    'body' => 'Body 101'
                ]
            ]
        ]);

        $response = $this->get('/api/list');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    '*' => [
                        'subject',
                        'body'
                    ]
                ]
            ])
            ->assertJson(function (AssertableJson $json) {
                $json->has('message')
                    ->has('status')
                    ->has('data.0', fn (AssertableJson $json) =>
                        $json->where('body', 'Body 101')
                            ->where('subject', 'Subject 101')
                            ->etc()
                    );
            });

    }
}
