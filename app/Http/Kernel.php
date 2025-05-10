<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
use App\Http\Middleware\NoCacheHeaders;

class Kernel extends HttpKernel
{
    /**
     * Global HTTP middleware stack.
     *
     * Runs during every request to your application.
     */
    protected $middleware = [
        NoCacheHeaders::class, // ✅ Applies no-cache headers to all responses
    ];

    /**
     * Route middleware groups.
     */
    protected $middlewareGroups = [
        'web' => [
            // Your existing Breeze middleware, if any
        ],

        'api' => [
            // Your API middleware, if any
        ],
    ];

    /**
     * Route-specific middleware.
     */
    protected $routeMiddleware = [
        'no.cache' => NoCacheHeaders::class,  // Define route middleware
    ];
}
