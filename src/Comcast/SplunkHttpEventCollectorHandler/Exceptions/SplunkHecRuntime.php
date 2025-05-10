<?php

namespace Comcast\SplunkHttpEventCollectorHandler\Exceptions;

use RuntimeException;
use Throwable;

class SplunkHecRuntime extends RuntimeException
{
    public const AUTHENTICATION_FAILED = 1000;
    public const NO_HEALTHY_HOST = 1001;
    public const CONNECTION_FAILED = 1002;
    public const SENDING_EVENT_FAILED = 1003;

    /**
     * Map of const Exception Codes to Exception
     *
     * @var array{int, string}
     */
    public static $map = [
        self::AUTHENTICATION_FAILED => 'Invalid authentication token or credentials.',
        self::NO_HEALTHY_HOST => 'Could not find a healthy host.',
        self::CONNECTION_FAILED => 'Connection to Splunk server failed.',
        self::SENDING_EVENT_FAILED => 'Sending the event to Splunk failed.',
    ];

    public static function make(int $code, ?Throwable $prev = null, ?string $msg = null): SplunkHecRuntime
    {
        $message = self::$map[$code];
        if ($msg !== null) {
            $message .= " $msg";
        }

        return new self($message, $code, $prev);
    }
}
