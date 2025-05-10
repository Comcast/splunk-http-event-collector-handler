<?php

namespace Comcast\SplunkHttpEventCollectorHandler\Tests;

use Comcast\SplunkHttpEventCollectorHandler\Config;

/**
 * @covers Comcast\SplunkHttpEventCollectorHandler\Config
 * @covers Comcast\SplunkHttpEventCollectorHandler\Env
 * @covers Comcast\SplunkHttpEventCollectorHandler\Frameworks
 */
class ConfigTest extends TestCase
{
    public function testItGetsAllConfig()
    {
        $this->assertEquals($this->generateConfig(), Config::getConfig());
    }

    public function testItGetsSpecificConfig()
    {
        $this->generateConfig();

        $this->assertEquals('my-token', Config::get('auth_token'));
    }

    public function testItGetsDefaultConfig()
    {
        $this->assertTrue(Config::get('enabled'));
        $this->assertEquals('localhost:8088', Config::get('host'));
        $this->assertNull(Config::get('auth_token'));
        $this->assertNull(Config::get('index'));
        $this->assertNull(Config::get('sourcetype'));
        $this->assertFalse(Config::get('use_indexing_acknowledgement'));
        $this->assertNull(Config::get('channel_id'));
        $this->assertTrue(Config::get('verify_tls'));
        $this->assertEquals(5, Config::get('timeout'));
    }
}
