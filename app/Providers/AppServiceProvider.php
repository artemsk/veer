<?php

namespace Veer\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        \Blade::setRawTags('{{', '}}');
        \Blade::setContentTags('{{{', '}}}');
        \Blade::setEscapedContentTags('{{{', '}}}');
    }
}
