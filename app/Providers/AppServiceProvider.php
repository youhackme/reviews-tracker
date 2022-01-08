<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\AppStores\AppleStore;
use GuzzleHttp\Client;

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
        $this->app->bind(AppleStore::class, function ($app) {
            return new AppleStore($app->make(Client::class));
        });


    }
}
