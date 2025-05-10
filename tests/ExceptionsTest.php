<?php

namespace Comcast\SplunkHttpEventCollectorHandler\Tests;

use Comcast\SplunkHttpEventCollectorHandler\Exceptions\SplunkHecLogic;
use Comcast\SplunkHttpEventCollectorHandler\Exceptions\SplunkHecRuntime;

/**
 * @covers Comcast\SplunkHttpEventCollectorHandler\Exceptions\SplunkHecLogic
 * @covers Comcast\SplunkHttpEventCollectorHandler\Exceptions\SplunkHecRuntime
 */
class ExceptionsTest extends TestCase
{
    public function testDefaultLogicException()
    {
        $this->expectException(SplunkHecLogic::class);
        $this->expectExceptionCode(SplunkHecLogic::NOT_LARAVEL);
        $this->expectExceptionMessage(SplunkHecLogic::$map[SplunkHecLogic::NOT_LARAVEL]);

        throw SplunkHecLogic::make(SplunkHecLogic::NOT_LARAVEL);
    }

    public function testCustomLogicException()
    {
        $message = 'testing a custom message';

        $this->expectException(SplunkHecLogic::class);
        $this->expectExceptionCode(SplunkHecLogic::NOT_LARAVEL);
        $this->expectExceptionMessage($message);

        throw SplunkHecLogic::make(SplunkHecLogic::NOT_LARAVEL, null, $message);
    }

    public function testDefaultRuntimeException()
    {
        $this->expectException(SplunkHecRuntime::class);
        $this->expectExceptionCode(SplunkHecRuntime::AUTHENTICATION_FAILED);
        $this->expectExceptionMessage(SplunkHecRuntime::$map[SplunkHecRuntime::AUTHENTICATION_FAILED]);

        throw SplunkHecRuntime::make(SplunkHecRuntime::AUTHENTICATION_FAILED);
    }

    public function testCustomRuntimeException()
    {
        $message = 'testing a custom message';

        $this->expectException(SplunkHecRuntime::class);
        $this->expectExceptionCode(SplunkHecRuntime::NO_HEALTHY_HOST);
        $this->expectExceptionMessage($message);

        throw SplunkHecRuntime::make(SplunkHecRuntime::NO_HEALTHY_HOST, null, $message);
    }
}
