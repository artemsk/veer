<?php

namespace Veer\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Veer\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        //\Veer\Http\Middleware\VerifyCsrfToken::class,
		\Veer\Http\Middleware\ShutdownMiddleware::class,
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Veer\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'guest' => \Veer\Http\Middleware\RedirectIfAuthenticated::class,
		'csrf' => \Veer\Http\Middleware\VerifyCsrfToken::class,
		'auth.admin' => \Veer\Http\Middleware\AuthenticateAdministrator::class,
		'early.view' => \Veer\Http\Middleware\EarlyResponseMiddleware::class,
	];

}
