<?php

namespace Comcast\SplunkHttpEventCollectorHandler\Tests;

use Comcast\SplunkHttpEventCollectorHandler\Config;
use Comcast\SplunkHttpEventCollectorHandler\Exceptions\SplunkHecRuntime;
use Comcast\SplunkHttpEventCollectorHandler\SplunkClient;
use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use Throwable;

/**
 * @covers Comcast\SplunkHttpEventCollectorHandler\SplunkClient
 * @covers Comcast\SplunkHttpEventCollectorHandler\Config
 * @covers Comcast\SplunkHttpEventCollectorHandler\Env
 * @covers Comcast\SplunkHttpEventCollectorHandler\Exceptions\SplunkHecRuntime
 * @covers Comcast\SplunkHttpEventCollectorHandler\Frameworks
 * @covers Comcast\SplunkHttpEventCollectorHandler\GuzzleClient
 *
 */
class SplunkClientTest extends TestCase
{
    public function testItThrowsExceptionWhenNoHostHealthy()
    {
        // Give it a bad host
        // Note: using "localhost" will sometimes cause unexpected errors
        // if there is something responding to localhost where these
        // tests are run. We use a uuid here for some randomness.
        $this->generateConfig(['host' => 'DDAD0A2C-0BBF-4CA6-BF50-5518A0CA9C9B']);

        $this->expectException(SplunkHecRuntime::class);
        $this->expectExceptionCode(SplunkHecRuntime::NO_HEALTHY_HOST);
        $this->expectExceptionMessage(SplunkHecRuntime::$map[SplunkHecRuntime::NO_HEALTHY_HOST]);

        $client = new SplunkClient();
        $client->sendEvent([]);
    }

    public function testItSendsDataWithoutAcknowledgement()
    {
        $token = file_get_contents(__DIR__.'/.token_without_ack');
        $this->generateConfig(['auth_token' => $token, 'sourcetype' => 'main', 'index' => 'main',]);

        $client = new SplunkClient();

        $promise = $client->sendEvent(['event' => 'testing from SplunkClientTest', 'index' => 'main', 'sourcetype' => 'main',]);

        $promise->wait();

        $this->assertTrue($promise->getState() === 'fulfilled');
    }

    public function testItProperlyHandlesExceptions()
    {
        $this->expectException(SplunkHecRuntime::class);

        $token = file_get_contents(__DIR__.'/.token_without_ack');
        $this->generateConfig(['auth_token' => $token, 'sourcetype' => 'main', 'index' => 'main',]);

        $client = new SplunkClient();

        $promise = $client->sendEvent(['event' => 'testing from SplunkClientTest', 'index' => 'mainnnn', 'sourcetype' => 'main',]);

        try {
            $promise->wait();
        } catch (Throwable $t) {
            if ($t instanceof ConnectException) {
                throw SplunkHecRuntime::make(SplunkHecRuntime::CONNECTION_FAILED, $t, $t->getMessage());
            }

            throw SplunkHecRuntime::make(SplunkHecRuntime::SENDING_EVENT_FAILED, $t, $t->getMessage());
        }
    }

    // public function testItFailsWithAcknowledgementWithoutChannel()
    // {
    //     $token = file_get_contents(__DIR__.'/.token_with_ack');
    //     $this->generateConfig(['auth_token' => $token, 'sourcetype' => 'main', 'index' => 'main', 'use_indexing_acknowledgement' => true]);
    //     $client = new SplunkClient();

    //     try {
    //         $client->sendEvent(['event' => 'testing from SplunkClientTest with index acknowledgement but supposedly without a data channel', 'index' => 'main', 'sourcetype' => 'main',]);
    //     } catch (Exception $exp) {
    //         $this->assertTrue(get_class($exp) === SplunkHecRuntime::class);
    //         $this->assertTrue(\str_contains($exp->getMessage(), 'Data channel is missing'));

    //         return;
    //     }

    //     $this->assertTrue(false, 'This test should have run other assertions and returned. Since it did not, the test with acknowledgement without channel failed.');
    // }

    // public function testItSendsDataWithAcknowledgement()
    // {
    //     $token = file_get_contents(__DIR__.'/.token_with_ack');
    //     $this->generateConfig(['hostname' => 'localhost', 'auth_token' => $token, 'use_indexing_acknowledgement' => true, 'channel_id' => 'ee8ff66d-92e1-40ef-8bbf-e1814a8f74e4', 'index' => 'main', 'sourcetype' => 'main',]);
    //     $client = new SplunkClient();

    //     $client->sendEvent(['event' => 'testing from SplunkClientTest', 'channel_id' => Config::get('channel_id')]);
    // }
}
