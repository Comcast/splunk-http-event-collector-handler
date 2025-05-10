<?php

namespace Comcast\SplunkHttpEventCollectorHandler\Exceptions;

use LogicException;
use Throwable;

class SplunkHecLogic extends LogicException
{
    public const NOT_LARAVEL = 1000;

    /**
     * Map of const Exception Codes to Exception
     *
     * @var array{int, string}
     */
    public static $map = [
        self::NOT_LARAVEL => "This method is specific to the Laravel Framework but it wasn't found.",
    ];

    public static function make(int $code, ?Throwable $prev = null, ?string $msg = null): SplunkHecLogic
    {
        $message = self::$map[$code];
        if ($msg !== null) {
            $message .= " $msg";
        }

        return new self($message, $code, $prev);
    }
}
