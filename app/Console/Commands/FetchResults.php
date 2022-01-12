<?php

namespace App\Console\Commands;

use App\AppStores\AppleStore;
use App\AppStores\GooglePlay;
use Illuminate\Console\Command;
use App\Models\Application;
use App\Models\Review;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FetchResults extends Command
{
    /**
     * Fetch Results of a searched term
     *
     * @var string
     */
    protected $signature = 'fetch:results
                            {--term=}
                            {--store=}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch results of a searched term';

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
        $term  = $this->option('term');
        $store = $this->option('store');

        $this->info('Fetching Results');

        $provider = ($store == 'apple') ? AppleStore::class : GooglePlay::class;

        $store = resolve($provider, [
            [
                'term'     => $term,
                'language' => 'en',
                'country'  => 'us',
                'limit'    => 5,
            ],
        ]);

        $results = $store->search();

        try {
            if ($results) {
                $results->each(function ($app) {
                    $this->info($app['name']);
                });
            } else {
                $this->error('No Result Found');
            }
        }
        catch (\Exception $error) {
            $this->error(json_encode($error));
        }

    }
}
