<?php


namespace App\Engine;


use App\Models\Application;
use App\Models\Review;
use App\AppStores\AppleStoreProvider;
use App\AppStores\GooglePlayStoreProvider;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class SaveStoreData
{
    public array $config;
    public string $store;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->store  = ($config['store'] === 'apple' ? AppleStoreProvider::class : GooglePlayStoreProvider::class);
    }

    public function reviews()
    {
        $store = resolve($this->store, $this->config);

        $reviews = $store->reviews();

        try {
            $application = Application::where('applications_id', $this->config['id'])->firstOrFail();

            if ($reviews) {
                $reviews->each(function ($review) use ($application) {

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

                });
            } else {
                return false;
            }
        }
        catch (ModelNotFoundException $error) {
            throw new \Exception('Application not registered.');
            return false;
        }


        return $reviews;
    }

    public function app()
    {
        $store = resolve($this->store, $this->config);

        $app = $store->app();
        if ($app) {

            Application::firstOrCreate(
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
                    'released_at'     => $app['released_at'],
                    'developer_id'    => $app['developer_id'],
                    'genre'           => $app['genre'],
                ]
            );
        }

        return $app;
    }
}