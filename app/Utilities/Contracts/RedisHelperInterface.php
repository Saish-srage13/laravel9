<?php

namespace App\Utilities\Contracts;

use Illuminate\Database\Eloquent\Model;

interface RedisHelperInterface {
    /**
     * Store the id of a message along with a message subject in Redis.
     *
     * @param  mixed  $id
     * @param  string  $messageSubject
     * @param  string  $toEmailAddress
     * @return void
     */
    public function store(string $id, mixed $data): void;
}
