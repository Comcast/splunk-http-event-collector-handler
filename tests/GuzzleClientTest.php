<?php

namespace Comcast\SplunkHttpEventCollectorHandler\Tests;

use Comcast\SplunkHttpEventCollectorHandler\Config;
use Comcast\SplunkHttpEventCollectorHandler\GuzzleClient;
use Comcast\SplunkHttpEventCollectorHandler\SplunkClient;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

/**
 * @covers Comcast\SplunkHttpEventCollectorHandler\GuzzleClient
 * @covers Comcast\SplunkHttpEventCollectorHandler\Config
 * @covers Comcast\SplunkHttpEventCollectorHandler\Env
 * @covers Comcast\SplunkHttpEventCollectorHandler\Frameworks
 */
class GuzzleClientTest extends TestCase
{
    public function testItCreatesGuzzleClientWithDefaultConfig()
    {
        $this->generateConfig();
        $this->expectException(ClientException::class);
        
        $client = GuzzleClient::make(Config::getConfig());
        $this->assertEquals(Client::class, get_class($client));

        $host = Config::config('host');
        $client->get("https://{$host}".SplunkClient::TIMESTAMPED_COLLECTOR_ENDPOINT);
    }
}
