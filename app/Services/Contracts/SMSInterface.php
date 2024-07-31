<?php

namespace App\Services\Contracts;

interface SMSInterface
{
    public static function sendText(string $phoneNumber, string $body);
}