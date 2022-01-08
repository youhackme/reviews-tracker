<?php

namespace App\Console\Commands;

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
                            {id=}
                            {store=}';


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
        $store = resolve(AppleStore::class);
        $result = $store->reviews();
        dd($result);
    }
}
