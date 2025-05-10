<?php

namespace Comcast\SplunkHttpEventCollectorHandler;

use GuzzleHttp\Client;

class GuzzleClient
{
    /**
     * Create a new Guzzle client based on our config
     *
     * @param array<string, string> $config
     */
    public static function make(array $config)
    {
        $general_header = ['ContentType' => 'application/json'];

        $auth_header = ['Authorization' => "Splunk {$config['auth_token']}",];

        // $channel_header = $config['use_indexing_acknowledgement'] ? $config['channel_id'] : [];
     
        // $headers = array_merge($general_header, $auth_header, $channel_header);
        $headers = array_merge($general_header, $auth_header);

        $options = array_merge(['verify' => $config['verify_tls'] === 1 ? true : false], ['timeout' => $config['timeout']], ['base_url' => ''], ['headers' => $headers]);

        return new Client($options);
    }
}
