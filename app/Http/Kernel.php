<?php namespace Veer\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel {

	/**
	 * The application's global HTTP middleware stack.
	 *
	 * @var array
	 */
	protected $middleware = [
		'Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode',
		'Illuminate\Cookie\Middleware\EncryptCookies',
		'Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse',
		'Illuminate\Session\Middleware\StartSession',
		'Illuminate\View\Middleware\ShareErrorsFromSession',
	//	'Veer\Http\Middleware\VerifyCsrfToken',
		'Veer\Http\Middleware\ShutdownMiddleware',
	];

	/**
	 * The application's route middleware.
	 *
	 * @var array
	 */
	protected $routeMiddleware = [
		'auth' => 'Veer\Http\Middleware\Authenticate',
		'auth.basic' => 'Illuminate\Auth\Middleware\AuthenticateWithBasicAuth',
		'guest' => 'Veer\Http\Middleware\RedirectIfAuthenticated',
		'csrf' => 'Veer\Http\Middleware\VerifyCsrfToken',
		'auth.admin' => 'Veer\Http\Middleware\AuthenticateAdministrator',
		'early.view' => 'Veer\Http\Middleware\EarlyResponseMiddleware',
	];

}
