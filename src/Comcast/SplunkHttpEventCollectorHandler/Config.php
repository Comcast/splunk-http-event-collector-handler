<?php

namespace Comcast\SplunkHttpEventCollectorHandler;

class Config
{
    /**
     * Retrieves config values based on type of framework
     *
     * @return array<string,string|array<string,string>>
     */
    public static function getConfig()
    {
        if (function_exists('config')) {
            return \config('splunk_hec_handler');
        }

        return self::transformBoolsNulls(require __DIR__.'/config/splunk_hec_handler.php');
    }

    /**
     * Gets config based on framework and retrieves a single key
     *
     * @return string|array<string,string>
     */
    public static function config(string $key, $default = null)
    {
        $config = collect(self::getConfig());

        return $config->get($key) ?? $default;
    }

    /**
     * Gets config based on framework and retrieves a single key
     *
     * @return string|array<string,string>
     */
    public static function get(string $key)
    {
        return self::config($key);
    }

    protected static function transformBoolsNulls(array $config)
    {
        $config = collect($config);
        $config->transform(function ($item) {
            if ($item === 'null') {
                return null;
            }

            if ($item === 'true') {
                return true;
            }

            if ($item === 'false') {
                return false;
            }

            return $item;
        });

        return $config->all();
    }
}
