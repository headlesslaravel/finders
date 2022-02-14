<?php

namespace HeadlessLaravel\Finders;

use Illuminate\Support\ServiceProvider;

class FindersProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/headless-finders.php', 'headless-finders');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/headless-finders.php' => config_path('headless-finders.php'),
        ], 'headless-finders-config');
    }
}
