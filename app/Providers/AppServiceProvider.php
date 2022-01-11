<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\AppStores\AppleStore;
use GuzzleHttp\Client;
use App\AppStores\GooglePlay;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(AppleStore::class, function ($app, $params) {
            return new AppleStore($app->make(Client::class), current($params));
        });

        $this->app->bind(GooglePlay::class, function ($app, $params) {
            return new GooglePlay($app->make(Client::class), current($params));
        });

    }
}
