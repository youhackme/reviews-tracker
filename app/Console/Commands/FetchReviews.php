<?php

namespace App\Console\Commands;

use App\AppStores\AppleStore;
use App\AppStores\GooglePlay;
use Illuminate\Console\Command;
use App\Models\Application;
use App\Models\Review;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
                            {--country=us}';
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

        $this->info('Fetching Reviews');
        $provider  = GooglePlay::class;
        $arguments = [
            [
                'id' => $id,
            ],
        ];

        if ($store == 'apple') {
            $arguments = [
                [
                    'id'      => $id,
                    'country' => $country,
                ],
            ];
            $provider  = AppleStore::class;
        }


        $store = resolve($provider, $arguments);


        $reviews = $store->reviews();


        try {
            $application = Application::where('applications_id', $id)->firstOrFail();

            if ($reviews) {
                $reviews->each(function ($review, $key) use ($application) {

                    Review::firstOrCreate(
                        [
                            'applications_id' => $application->id,
                            'reviews_id'      => $review['id'],
                        ],
                        [
                            'applications_id' => $application->id,
                            'reviews_id'      => $review['id'],
                            'version'         => $review['version'],
                            'url'             => $review['url'],
                            'author'          => $review['author'],
                            'title'           => $review['title'],
                            'description'     => $review['description'],
                            'score'           => $review['score'],
                            'votes'           => $review['votes'],
                            'country'         => $review['country'],
                            'reviewed_at'     => $review['reviewed_at'],
                        ]);

                    $this->info('[' . $review['reviewed_at'] . ']' . '[' . $review['id'] . ']' . $review['title']);
                });
            } else {
                $this->error('No Reviews Found');
            }
        }
        catch (ModelNotFoundException $error) {
            $this->error("Application $id needs to be registered before fetching reviews");
        }


    }
}
