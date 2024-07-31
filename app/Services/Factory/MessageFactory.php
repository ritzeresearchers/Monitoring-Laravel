<?php

namespace App\Services\Factory;

use InvalidArgumentException;

class MessageFactory
{
    public const BUSINESS = 'business';
    public const CUSTOMER = 'customer';

    public static function createMessage(string $type, string $name, bool $addLink = false): string
    {
        $message = "";

        switch ($type) {
            case self::BUSINESS:
                $message = sprintf(
                    'Unfortunately,%s,has declined the inquiry,kindly wait for further quotations from other Service Providers.
                            Thank you.',
                    $name
                );
                break;

            case self::CUSTOMER:
                $message = sprintf(
                    'Unfortunately,%s,has declined the quotation,kindly wait for Job Postings from other Customers.
                            Thank you.',
                    $name
                );
                break;

            default:
                throw new InvalidArgumentException("Invalid message type provided.");
        }

        if ($addLink) {
            $message .= "\n" . config('config.appBaseUrl');
        }

        return $message;
    }
}
