<?php

namespace Comcast\SplunkHttpEventCollectorHandler\Tests;

use Comcast\SplunkHttpEventCollectorHandler\GuzzleClient;
use PHPUnit\Framework\TestCase as BaseTest;

class TestCase extends BaseTest
{
    public function tearDown(): void
    {
        $_ENV = [];
    }

    /**
     * Generates config that the Env class can use.
     * See fillConfig method for defaults.
     *
     * @param array<string, mixed> $config
     *
     * @return array
     */
    protected function generateConfig(array $config = []): array
    {
        $config = $this->fillConfig($config);
        $_ENV['SPLUNK_HEC_ENABLED'] = $config['enabled'];
        $_ENV['SPLUNK_HEC_HOST'] = $config['host'];
        $_ENV['SPLUNK_HEC_TOKEN'] = $config['auth_token'];
        $_ENV['SPLUNK_HEC_INDEX'] = $config['index'];
        $_ENV['SPLUNK_HEC_SOURCETYPE'] = $config['sourcetype'];
        $_ENV['SPLUNK_HEC_USE_INDEXING_ACKNOWLEDGEMENT'] = $config['use_indexing_acknowledgement'];
        $_ENV['SPLUNK_HEC_VERIFY_TLS'] = $config['verify_tls'];
        $_ENV['SPLUNK_HEC_TIMEOUT'] = $config['timeout'];

        if (isset($config['channel_id'])) {
            $_ENV['SPLUNK_HEC_CHANNEL_ID'] = $config['channel_id'];
        }

        return [
            # Enables Splunk HEC Log Handling
            'enabled' => $config['enabled'],
        
            # The Splunk HEC host and port.
            # Single host or Multiple with `,`.
            # Example, "host1:8088,host2:8088,host3:8096"
            'host' => $config['host'],
        
            # The auth token to use for authentication
            'auth_token' => $config['auth_token'],
        
            # The index to send events to
            'index' => $config['index'],
        
            # The sourcetype to send data to.
            'sourcetype' => $config['sourcetype'],
        
            # Tell Splunk to Acknowledge when submitted data is indexed.
            # NOTE: This must be enabled for your token by your Splunk admins!
            # Not implemented yet
            'use_indexing_acknowledgement' => $config['use_indexing_acknowledgement'],

            'channel_id' => $config['channel_id'],
        
            # Whether to verify TLS or not. Not implemented yet.
            'verify_tls' => $config['verify_tls'],
        
            # The number of seconds to wait before trying again
            'timeout' => (int) $config['timeout'],

            # The message to log in case we can't send to Splunk HEC
            'emergency_message' => $config['emergency_message'],
            'emergency_channel' => $config['emergency_channel'],
        ];
    }

    /**
     * Takes any values passed as wanted config then fills in defaults that weren't set
     *
     * @param array<string, mixed> $config
     *
     * @return array< string, mixed>
     */
    protected function fillConfig(array $config): array
    {
        if (!isset($config['enabled'])) {
            $config['enabled'] = true;
        }

        if (!isset($config['host'])) {
            $config['host'] = 'localhost:8088';
        }

        if (!isset($config['auth_token'])) {
            $config['auth_token'] = 'my-token';
        }

        if (!isset($config['index'])) {
            $config['index'] = 'my-index';
        }

        if (!isset($config['sourcetype'])) {
            $config['sourcetype'] = 'my-sourcetype';
        }

        if (!isset($config['use_indexing_acknowledgement'])) {
            $config['use_indexing_acknowledgement'] = false;
            $config['channel_id'] = null;
        }

        // Note: we don't set a default for channel_id since we want that to be included by the consumer.

        if (!isset($config['verify_tls'])) {
            $config['verify_tls'] = false;
        }

        if (!isset($config['timeout'])) {
            $config['timeout'] = 5;
        }

        if (!isset($config['emergency_message'])) {
            $config['emergency_message'] = 'An error occurred while sending logs to Splunk HEC. Please review immediately!';
        }

        if (!isset($config['emergency_channel'])) {
            $config['emergency_channel'] = 'single';
        }

        return $config;
    }

    /**
     * Uses the Splunk API to search
     *
     * @param array<string, mixed> $config
     */
    protected function getFromSplunk(string $search, array $config)
    {
        $client = GuzzleClient::make($config);
        $response = $client->withOptions([
            'data' => [
                'search' => 'search index=main sourcetype=main earliest=-1h',
                'output_mode' => 'json',
            ]
        ]);
        var_dump($response);
        die();
    }
}
