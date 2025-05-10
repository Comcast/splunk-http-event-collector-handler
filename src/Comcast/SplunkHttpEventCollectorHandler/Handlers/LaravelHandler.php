<?php

namespace Comcast\SplunkHttpEventCollectorHandler\Handlers;

use Comcast\SplunkHttpEventCollectorHandler\Config;
use Comcast\SplunkHttpEventCollectorHandler\Exceptions\SplunkHecLogic;
use Comcast\SplunkHttpEventCollectorHandler\Exceptions\SplunkHecRuntime;
use Comcast\SplunkHttpEventCollectorHandler\LogRecord;
use Comcast\SplunkHttpEventCollectorHandler\SplunkClient;
use Illuminate\Support\Facades\Log;
use Monolog\Handler\HandlerInterface;
use Monolog\LogRecord as MonologRecord;
use Throwable;

class LaravelHandler extends AbstractHandler implements HandlerInterface
{
    /**
     * Handles a record.
     *
     * All records may be passed to this method, and the handler should discard
     * those that it does not want to handle.
     *
     * The return value of this function controls the bubbling process of the handler stack.
     * Unless the bubbling is interrupted (by returning true), the Logger class will keep on
     * calling further handlers in the stack with a given log record.
     *
     * @param array{int, string} $record The record to handle
     *
     * true means that this handler handled the record, and that bubbling is not permitted.
     * false means the record was either not processed or that this handler allows bubbling.
     */
    public function handle(MonologRecord $record): bool
    {
        $record = (array) $record;

        if (!Config::config('enabled')) {
            return false;
        }

        $client = new SplunkClient();
        $client->sendEvent(LogRecord::parse($record));

        return true;
    }

    protected function logEmergency(Throwable $thr)
    {
        Log::channel(Config::config('emergency_channel'))->emergency(Config::config('emergency_message').'Exception type: '.get_class($thr).'; Exception code: '.$thr->getCode().'; Message: '.$thr->getMessage());
    }
}
