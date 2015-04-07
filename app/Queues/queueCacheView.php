<?php namespace Veer\Queues;

class queueCacheView {

    /* name */
    public $queueName = "Cache Views";

    /* description */
    public $queueDescription = "Cache views to have something to show if DB fails.";


    public function fire($job, $data)
    {
        if (isset(app('veer')->cachedView) && config('veer.htmlcache_enable') == true && !auth_check_session()) {

        $cache_url = cache_current_url_value();

        $expiresAt = now(24, 'hours');

        \Cache::has($cache_url) ?: \Cache::add($cache_url, app('veer')->cachedView->render(), $expiresAt);

        } else {
            return $job->release();
        }
                      
        // leave it here
        if(isset($data['repeatJob']) && $data['repeatJob'] > 0) {
                $job->release( $data['repeatJob'], 'minutes');
        }
    }


}