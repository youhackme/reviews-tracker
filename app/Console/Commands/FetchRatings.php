<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\AppStores\AppleStore;

class FetchRatings extends Command
{
    /**
     * Fetch details of a specific App
     *
     * @var string
     */
    protected $signature = 'fetch:ratings
                            {--id=}
                            {--store=}';
//id for testing:1205990992, 1600880394, 1586321858

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch ratings for a specific App';

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
        $this->info('Fetching Ratings');

        $store = resolve(AppleStore::class, [
            'id' => (int)$this->option('id'),
        ]);

        $ratings = $store->ratings();

        if ($ratings) {
            $ratings->each(function ($rating, $key) {
                $star = key($rating);
                $this->info($star . '. ' . $rating[$star]);
            });
        } else {
            $this->error('No Star rating Found');
        }
    }
}
