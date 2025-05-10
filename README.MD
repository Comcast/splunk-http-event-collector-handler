# Splunk HTTP Event Collector Handler

## Introduction
[Splunk](https://splunk.com) is a platform to search, analyze, and visualize data. The standard way to push data to Splunk is to use the [Universal Forwarder](https://www.splunk.com/en_us/resources/videos/splunk-education-getting-data-in-with-forwarders.html) which can monitor a log file and forward records to Splunk in near-real-time. When this isn't an option, for example when deploying an app to an environment you can't directly control, Splunk's [HTTP Event Collector](https://docs.splunk.com/Documentation/Splunk/latest/Data/UsetheHTTPEventCollector) (HEC) can be used instead. HEC lets applications send events to Splunk via a web API.

This package is a Splunk HEC client using [guzzlehttp/guzzle](https://github.com/guzzle/guzzle) and implements [Monolog's](https://github.com/Seldaek/monolog) [HandlerInterface](https://github.com/Seldaek/monolog/blob/main/src/Monolog/Handler/HandlerInterface.php) to easily add the Splunk HEC Handler to a project already using Monolog.

## Installation
Install this package with [composer](https://getcomposer.org):
```bash
composer require comcast/splunk-http-event-collector-handler
```

## Configuration

### Laravel-Specific Configuration
For projects using the [Laravel Framework](https://laravel.com), the `LaravelServiceProvider` will be automatically discovered. If you use configuration caching in any of your environments, you'll have to publish the config with `php artisan vendor:publish --provider="Comcast\SplunkHttpEventCollectorHandler\LaravelServiceProvider"`. Otherwise, values from `.env` that aren't referenced in a Laravel config file will not be cached so they will evaluate as `null` ([source](https://andy-carter.com/blog/env-gotcha-in-laravel-when-caching-configuration)).

### Generic Configuration
This package uses `vlucas/dotenv` and the $_ENV supervariable in order to [avoid a dependency on `illuminate/support`](https://mattallan.me/posts/dont-use-illuminate-support/) to pull configuration values from a `.env` file.

If you do not already use a `.env` file in your project, create one at the root level, then add the following values:

```bash
SPLUNK_HEC_ENABLED= # bool, is the Monolog handler enabled. Default: true
SPLUNK_HEC_HOST= # string, the hostname of the Splunk instance along with the port, like `splunk.com:8088`
SPLUNK_HEC_TOKEN= # string, the authentication token
SPLUNK_HEC_INDEX= # string, the index to send events to
SPLUNK_HEC_SOURCETYPE= # string, the sourcetype to send events to
SPLUNK_HEC_USE_INDEXING_ACKNOWLEDGEMENT= # bool, whether Splunk should acknowledge the event was indexed # NOT YET IMPLEMENTED. Default: false
SPLUNK_HEC_VERIFY_TLS= # bool, whether the Splunk's TLS cert should be verified. Default: true
```

Some of these values have defaults, listed at the end of the description. If you wish to use the defaults, you can leave these as-is (nothing after the `=`) or remove them from your .env file entirely. Then set the values correctly for each of your environments (if needed).

_NOTE_: Indexing Acknowledgement is not implemented yet. Setting this value to true or false has no effect at this time.

## Usage

### Using the Client Class directly
You can use the `Client` class directly, if desired, to send information to Splunk HEC yourself:

```php
use Comcast\SplunkHttpEventCollectorHandler\SplunkClient;

$client = new SplunkClient();
if (!$client->sendEvent([
    'event' => [
        'your data here',
    ],
    'index' => 'my-index', // You can set the index for this request here, or use the default for your HEC Token
    'sourcetype' => 'my-sourcetype', // You can set the sourcetype for this request here, or use the default for your HEC Token
])) {
    $error = $client->getError();
}
```

The `sendEvent` method returns a bool that represents success/failure. The response object is stored within the SplunkClient object and can be retrieved with `$client->getResponse()`. Returns the response object or null.

The `SplunkClient` class checks the HEC Health endpoint before sending data, so you can provide a [GSLB](https://www.cloudflare.com/learning/cdn/glossary/global-server-load-balancing-gslb/), or a [Round-Robin DNS record]().

### Using a Custom Monolog Handler

Alternatively, you could configure this package as a Monolog Handler.

#### Monolog Handler in Laravel

The various logging channels are defined in your `config/logging.php` file. At the bottom, you'll see a `channels` array that looks something like:

```php
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['syslog', 'slack'],
    ],
'splunk_hec' =>
    'syslog' => [
        'driver' => 'syslog',
        'level' => 'debug',
    ],

    'slack' => [
        'driver' => 'slack',
        'url' => env('LOG_SLACK_WEBHOOK_URL'),
        'username' => 'Laravel Log',
        'emoji' => ':boom:',
        'level' => 'critical',
    ],
],
```

In the `channels` array, create another key and give it a identifiable name like `splunk` or `splunk_hec` etc. using the `monolog` driver and the `Handler` class from this package:
```php
// use Comcast\SplunkHttpEventCollectorHandler\Handlers\LaravelHandler;
'channels' => [
    'splunk_hec' => [
        'driver' => 'monolog',
        'level' => 'debug',
        'handler' => LaravelHandler::class,
    ],
]
```

Now, we can use this channel to send logs to Splunk HEC: `Log::channel('splunk_hec')->info('a log line');`. If you need to change the index or sourcetype, you can use the second parameter, the `context` array: `Log::channel('splunk_hec')->info('a log line', ['index' => 'my-index', 'sourcetype' => 'my-sourcetype']);`. You can put any other information in the `context` array as well, and it will show up in your data in Splunk. The `LaravelHandler` class `unset`'s these two keys from the array before sending the log.

More information about logging can be found in the [docs](https://laravel.com/docs/8.x/logging), but at the top of the file you'll see a `default` key where you can set the default channel when using the `Illuminate\Support\Facades\Log` facade like `Log::info('a log line')`.

 #### Note: if this is your primary logging channel, it's better to use the `stack` driver so that you can specify multiple channels in case the request to Splunk HEC fails for any reason (credentials fail, all hosts are down, etc.), the log will still end up in the other channels. For example, specify `splunk_hec` and `single` to send logs to Splunk HEC and to `storage_path('logs/laravel.log')`

## Testing

Tests for this library require a running Splunk instance. Using [Splunk Lab](https://github.com/dmuth/splunk-lab) we can start and stop a local instance for testing.

For convenience, you can start and stop the Splunk instance and run the tests using Composer scripts:
```bash
composer run-script start-splunk
composer run-script tests
composer run-script stop-splunk
```

For now, these scripts assume they're run on a *nix environment including MacOS. For Windows support please consider submitting a Pull Request (see below).

## Help Wanted
Though you can use the Client class directly in any framework (or without a framework), the Handler class is currently limited by my own experience. Having used Laravel for years, I've added support for Laravel, but would like to provide support for other frameworks. If you know how to do so, please consider contributing to the project.

## Contributing
Contributions are welcome! If you'd like to contribute to this project, please read the [Code of Conduct](CODE_OF_CONDUCT.md) first, then see the [CONTRIBUTING](CONTRIBUGING.md) details.

## Credits
A full list of contributors is available [here](CONTRIBUTORS.md)!