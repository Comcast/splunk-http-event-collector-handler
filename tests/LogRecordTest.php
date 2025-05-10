<?php

namespace Comcast\SplunkHttpEventCollectorHandler\Tests;

use Carbon\Carbon;
use Comcast\SplunkHttpEventCollectorHandler\LogRecord;
use Monolog\DateTimeImmutable;

/**
 * @covers Comcast\SplunkHttpEventCollectorHandler\LogRecord
 * @covers Comcast\SplunkHttpEventCollectorHandler\Config
 * @covers Comcast\SplunkHttpEventCollectorHandler\Env
 * @covers Comcast\SplunkHttpEventCollectorHandler\Frameworks
 */
class LogRecordTest extends TestCase
{
    public function testItTransformsLogRecords()
    {
        $this->generateConfig();
        
        $record = [
            'message' => 'This is a record for testing',
            'level' => 200,
            'level_name' => 'INFO',
            'channel' => 'testing',
            'datetime' => new DateTimeImmutable(true),
            'context' => [],
            'extra' => [],
        ];

        $timestamp = (string) Carbon::instance($record['datetime'])->toDateTimeString();
        $context = json_encode($record['context']);

        $expected = [
            'event' => "[{$timestamp}] {$record['channel']}.{$record['level_name']}: {$record['message']} | {$context}",
            'sourcetype' => 'my-sourcetype',
            'index' => 'my-index',
        ];

        $this->assertEquals($expected, LogRecord::parse($record));
    }
}
