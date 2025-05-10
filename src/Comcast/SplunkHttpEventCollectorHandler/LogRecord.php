<?php

namespace Comcast\SplunkHttpEventCollectorHandler;

use Carbon\Carbon;
use Comcast\SplunkHttpEventCollectorHandler\Config;

class LogRecord
{
    /**
     * Parses the record for Splunk.
     * Record Example:
     *array:7 [
     *    "message" => "just a test"
     *    "context" => array:1 [
     *        "woohoo" => array:1 [
     *            0 => true
     *        ]
     *    ]
     *    "level" => 200
     *    "level_name" => "INFO"
     *    "channel" => "local"
     *    "datetime" => Monolog\DateTimeImmutable @1616612547^ {#3618
     *        -useMicroseconds: true
     *        date: 2021-03-24 19:02:27.671643 UTC (+00:00)
     *    }
     *    "extra" => []
     *]
     *
     * @param array<string, string|array<string, string>> $record
     *
     * @return array<string, string>
     */
    public static function parse(array $record): array
    {
        $timestamp = (string) Carbon::instance($record['datetime'])->toDateTimeString();
        $context = $record['context'];
        $sourcetype = $context['sourcetype'] ?? Config::config('sourcetype');
        $index = $context['index'] ?? Config::config('index');

        $context = json_encode(self::cleanContext((array) $context));
        $channel = $record['channel'];
        $level_name = $record['level_name'] ?? 'INFO';
        $message = $record['message'];

        $text = "[{$timestamp}] {$channel}.{$level_name}: {$message} | {$context}";

        return [
            'event' => $text,
            'sourcetype' => $sourcetype,
            'index' => $index,
        ];
    }

    /**
     * Unsets some info from the context array that isn't needed in the log
     *
     * @param array<string, string> $context
     *
     * @return array<string, string>
     */
    protected static function cleanContext(array $context): array
    {
        unset($context['identifer']);

        unset($context['sourcetype']);

        unset($context['index']);

        return $context;
    }
}
