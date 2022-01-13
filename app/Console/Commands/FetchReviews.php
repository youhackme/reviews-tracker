<?php

namespace App\Console\Commands;

use App\AppStores\AppleStoreProvider;
use App\AppStores\GooglePlayStoreProvider;
use App\Engine\SaveStoreData;
use Illuminate\Console\Command;


class FetchReviews extends Command
{
    /**
     * Fetch Reviews, Ratings, App details
     *
     * @var string
     */
    protected $signature = 'fetch:reviews
                            {--id=}
                            {--store=}
                            {--country=us}
                            {--save}';
//id for testing:1205990992, 1600880394, 1586321858

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Reviews for a specific App';

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
        $id      = $this->option('id');
        $store   = $this->option('store');
        $country = $this->option('country');
        $save    = $this->option('save');

        $this->info('Fetching Reviews');


        $provider = ($store == 'apple') ? AppleStoreProvider::class : GooglePlayStoreProvider::class;

        $arguments = [
            'id'    => $id,
            'store' => $store,
        ];

        if ($store == 'apple') {
            $arguments['country'] = $country;
        }

        if ($save === true) {
            $provider = SaveStoreData::class;
        }

        $store   = resolve($provider, $arguments);
        $reviews = $store->reviews();

        if ($reviews) {
            $reviews->each(function ($review) {
                $this->info('[' . $review['reviewed_at'] . ']' . '[' . $review['id'] . ']' . $review['description']);
            });
        } else {
            $this->error('No Reviews Found');
        }
    }
}
