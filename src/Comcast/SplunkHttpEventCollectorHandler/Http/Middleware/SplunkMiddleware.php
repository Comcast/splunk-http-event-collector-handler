<?php

namespace Comcast\SplunkHttpEventCollectorHandler\Http\Middleware;

use Closure;
use Comcast\SplunkHttpEventCollectorHandler\PromiseManager;
use Illuminate\Support\Facades\App;

class SplunkMiddleware
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    public function terminate($request, $response)
    {
        $manager = App::make(PromiseManager::class);
        $manager->resolvePromises();
    }
}