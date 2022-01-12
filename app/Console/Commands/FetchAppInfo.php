<?php

namespace App\Console\Commands;

use App\AppStores\GooglePlay;
use Illuminate\Console\Command;
use App\AppStores\AppleStore;
use App\Models\Application;
use Carbon\Carbon;

class FetchAppInfo extends Command
{
    /**
     * Fetch details of a specific App
     *
     * @var string
     */
    protected $signature = 'fetch:app
                            {--id=}
                            {--store=}';
//id for testing:1205990992, 1600880394, 1586321858

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Info for a specific App';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $store = $this->option('store');
        $id    = $this->option('id');


        $this->info('Fetching Info');


        $provider = ($store == 'apple') ? AppleStore::class : GooglePlay::class;

        $store = resolve($provider, [
            [
                'id' => $id,
            ],
        ]);

        $apps = $store->app();


        if ($apps) {
            $apps->each(function ($app, $key) {

                $application = Application::firstOrCreate(
                    ['applications_id' => $app['id']],
                    [
                        'applications_id' => $app['id'],
                        'name'            => $app['name'],
                        'screenshots'     => $app['screenshots'],
                        'icon'            => $app['icon'],
                        'developer_url'   => $app['developer_url'],
                        'languages'       => $app['languages'],
                        'reviews'         => $app['reviews'],
                        'score'           => $app['score'],
                        'url'             => $app['url'],
                        'released_at'     => $app['released_on'],
                        'developer_id'    => $app['developer_id'],
                        'genre'           => $app['genre'],
                    ]
                );

                $this->info($application['id'] . '. ' . $application['name']);
            });
        } else {
            $this->error('No App Found');
        }
    }
}
