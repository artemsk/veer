<?php namespace Veer\Services;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class CachingQueries  {
	
	protected $query;

	public function __construct(Builder $query = null)
	{
		$this->query = $query;
	}
	
	/* replace with new query */
	public function make(Builder $query)
	{
		$this->query = $query;
	}
	
	/* replace with new query */
	public function makeAndRemember(Builder $query, $method, $minutes, $options = null, $key = null)
	{
		$this->query = $query;
		
		return $this->remember($minutes, $method, $options, $key);
	}
	
	/* generate cache key */
	public function generateCacheKey()
	{
		return md5('veercachesql'.$this->query->toSql().serialize($this->query->getBindings()));
	}
	
	/* get cache key */
	public function getCacheKey($key = null)
	{
		return !empty($key) ? $key : $this->generateCacheKey();
	}
	
	/* lists method */
	public function lists($column, $key = null, $minutes = -1)
	{
		if($minutes > 0)
		{
			return $this->cacheRemember($minutes, function() use ($column, $key) {
				return $this->query->lists($column, $key);
			}, $key);
		}
		
		return $this->cacheRememberForever(function() use ($column, $key) {
				return $this->query->lists($column, $key);
		}, $key);
	}	
	
	/* remember */
	public function remember($minutes, $method, $options = null, $key = null)
	{
		return $this->cacheRemember($minutes,  function() use ($method, $options) {
			return !empty($options) ? $this->query->{$method}($options) : $this->query->{$method}();
		}, $key);
	}
	
	/* remember forever */
	public function rememberForever($method, $options = null, $key = null)
	{
		return $this->cacheRememberForever(function() use ($method, $options) {
			return !empty($options) ? $this->query->{$method}($options) : $this->query->{$method}();
		}, $key);
	}
	
	/* cache */
	public function cacheRemember($minutes, Closure $callback, $key = null)
	{
		return \Cache::remember($this->getCacheKey($key), $minutes, $callback);
	}

	/* cache forever */
	public function cacheRememberForever(Closure $callback, $key = null)
	{
		return \Cache::rememberForever($this->getCacheKey($key), $callback);
	}	
}