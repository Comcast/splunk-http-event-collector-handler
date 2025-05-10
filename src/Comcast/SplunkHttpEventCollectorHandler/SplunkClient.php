<?php

namespace Comcast\SplunkHttpEventCollectorHandler;

use Comcast\SplunkHttpEventCollectorHandler\Exceptions\SplunkHecRuntime;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\App;
use Throwable;

class SplunkClient
{
    public const COLLECTOR_ENDPOINT = '/services/collector/1.0';
    public const ACK_ENDPOINT = '/services/collector/ack/1.0';
    public const HEALTH_ENDPOINT = '/services/collector/health/1.0';
    public const RAW_COLLECTOR = '/services/collector/raw/1.0';
    public const TIMESTAMPED_COLLECTOR_ENDPOINT = '/services/collector/event/1.0';

    /**
     * @var array<string,string> $error
     */
    protected array $error;

    protected string $host;

    protected int $max_tries;

    public function __construct()
    {
        $this->host = Config::config('host');
        $this->max_tries = Config::config('max_tries', 2);
    }

    /**
     * Gets any error that has been stored
     *
     * @return array{string, string|array{int, string}}
     */
    public function getError(): array
    {
        return $this->error;
    }

    /**
     * Sends log data to Splunk
     *
     * @param array<string,string|array<string,string>> $data
     *
     * @throws SplunkHecRuntime
     */
    public function sendEvent(array $data, $tries = 0)
    {

        $data = $this->setIndex($data);
        $data = $this->setSourcetype($data);

        $promise = $this->sendAsync($data);

        $manager = App::make(PromiseManager::class);
        $manager->push($promise);

        $promise->then(null, function (Throwable $t) use ($data, $tries) {
            if ($tries < $this->max_tries) {
                return $this->sendEvent($data, ++$tries);
            }

            $response = $this->sendSync($data);
            if ($response->getStatusCode() === 200) {
                return $response;
            }

            throw SplunkHecRuntime::make(SplunkHecRuntime::SENDING_EVENT_FAILED, $t, $t->getMessage());
        });

        return $promise;
    }

    protected function sendAsync(array $data): Promise
    {
        $client = GuzzleClient::make(Config::getConfig());

        return $client->postAsync("https://{$this->host}".self::TIMESTAMPED_COLLECTOR_ENDPOINT, [
            'json' => $data,
        ]);
    }

    protected function sendSync(array $data): Response
    {
        $client = GuzzleClient::make(Config::getConfig());

        return $client->post("https://{$this->host}".self::TIMESTAMPED_COLLECTOR_ENDPOINT, [
            'json' => $data,
        ]);
    }

    protected function setIndex(array $data)
    {
        if (!isset($data['index'])) {
            $data['index'] = Config::get('index');
        }

        return $data;
    }

    protected function setSourcetype(array $data)
    {
        if (!isset($data['sourcetype'])) {
            $data['sourcetype'] = Config::get('sourcetype');
        }

        return $data;
    }
}
