<?php

namespace Comcast\SplunkHttpEventCollectorHandler;

use GuzzleHttp\Promise\Promise;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Throwable;

class PromiseManager
{
    /**
     * Collection of Promises
     *
     * @var Collection
     */
    protected Collection $promises;

    public function __construct()
    {
        $this->promises = collect([]);
    }

    public function push(Promise $promise)
    {
        $this->promises->push($promise);
    }

    public function getPromises()
    {
        return $this->promises;
    }

    public function resolvePromises()
    {
        while($this->getPromises()->count() > 0) {
            $promise = $this->getPromises()->pop();
            try {
                $response = $promise->wait();
            } catch (Throwable $t) {
                Log::channel(Config::get('splunk_hec_handler.emergency_channel'))->emergency('Splunk HTTP Event Collector Promise failed to resolve. Error was: '.$t->getMessage());
            }
        }
    }
}
