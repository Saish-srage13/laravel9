<?php

namespace App\Enum;

enum EmailDeliveryStatus : string
{
    case PENDING = 'pending';
    case QUEUED = 'queued';
    case SENT = 'sent';
    case FAILED = 'failed';

    public static function fromValue(string $name): EmailDeliveryStatus
    {
        foreach (self::cases() as $status) {
            if( $name === $status->value ){
                return $status;
            }
        }
        throw new \ValueError("$name is not a valid backing value for enum " . self::class );
    }
}