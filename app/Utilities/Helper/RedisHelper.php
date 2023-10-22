<?php

namespace App\Utilities\Helper;

use App\Utilities\Contracts\RedisHelperInterface;
use Illuminate\Support\Facades\Redis;

class RedisHelper implements RedisHelperInterface
{
    public function store(string $id, mixed $data): void
    {
        Redis::hmset($id, $data);
    }
}