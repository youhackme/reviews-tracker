<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client;
use App\AppStores\GooglePlayStoreProvider;
use App\AppStores\AppleStoreProvider;
use App\Engine\SaveStoreData;
use App\Engine\GooglePlayStore;

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
        $this->app->bind(AppleStoreProvider::class, function ($app, $params) {
            return new AppleStoreProvider($app->make(Client::class), $params);
        });

        $this->app->bind(GooglePlayStoreProvider::class, function ($app, $params) {
            return new GooglePlayStoreProvider($app->make(Client::class), $params);
        });

        $this->app->bind(SaveStoreData::class, function ($app, $params) {
            return new SaveStoreData($params);
        });
    }
}
