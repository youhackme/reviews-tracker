<?php

namespace App\Console\Commands;

use App\AppStores\GooglePlay;
use Illuminate\Console\Command;
use App\AppStores\AppleStore;

class FetchReviews extends Command
{
    /**
     * Fetch Reviews, Ratings, App details
     *
     * @var string
     */
    protected $signature = 'fetch:reviews
                            {--id=}
                            {--store=}';
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
        $this->info('Fetching Reviews');

        $provider = $this->option('store');
        $provider === 'apple' ? AppleStore::class : GooglePlay::class;

        $store = resolve($provider, [
            'id' => (int)$this->option('id'),
        ]);


        $reviews = $store->reviews();
        if ($reviews) {
            $reviews->each(function ($review, $key) {
                $position = $key + 1;
                $this->info($position . '. ' . $review['title']);
            });
        } else {
            $this->error('No Reviews Found');
        }
    }
}
