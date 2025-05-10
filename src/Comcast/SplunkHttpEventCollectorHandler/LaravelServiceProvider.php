<?php

namespace Comcast\SplunkHttpEventCollectorHandler;

use Illuminate\Support\ServiceProvider;

class LaravelServiceProvider extends ServiceProvider
{
    /**
     * The name of the config file
     */
    private $config_file_name = 'splunk_hec_handler.php';

    /**
     * The path to the config file in this package
     */
    private $config_file_path = __DIR__.'/config/splunk_hec_handler.php';
    public function boot(): void
    {
        $this->publishes([
            $this->config_file_path => \config_path($this->config_file_name),
        ]);
    }

    public function register()
    {
        $this->app->singleton(PromiseManager::class, function ($app) {
            return new PromiseManager();
        });
    }
}
