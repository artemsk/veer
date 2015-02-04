<?php namespace Veer\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler {

	/**
	 * A list of the exception types that should not be reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		'Symfony\Component\HttpKernel\Exception\HttpException',
		'Illuminate\Database\Eloquent\ModelNotFoundException'		
	];

	/**
	 * Report or log an exception.
	 *
	 * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
	 *
	 * @param  \Exception  $e
	 * @return void
	 */
	public function report(Exception $e)
	{
		if($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) \Log::error("URL Not found ". app('url')->full());
		
		if($e instanceof \PDOException) \Log::error("Error connecting to database");
		
		if($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
			\Log::error("Unable to find site: " . app('url')->full());
		}
				
		return parent::report($e);
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Exception  $e
	 * @return \Illuminate\Http\Response
	 */
	public function render($request, Exception $e)
	{
		if($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) return \Redirect::route('404');
		
		if($e instanceof \PDOException) return $this->registerDbErrorHandler();
		
		if($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) return response("Be right back!", 503);
		
		if ($this->isHttpException($e))
		{
			return $this->renderHttpException($e);
		}
		else
		{
			return parent::render($request, $e);
		}
	}
	
	/**
	 * Database connection error
	 * 
	 * @return Response::make
	 */
	protected function registerDbErrorHandler()
	{
		$cache_url = cache_current_url_value();

		if (\Cache::has($cache_url)) {

			$cachedPage = \Cache::get($cache_url);
			
			return response()->view('dummy', array('cachedPage' => $cachedPage));
			
		} else {
			return response("Error connecting to database. Please come back later. ", 503);
		}

	}	

}
