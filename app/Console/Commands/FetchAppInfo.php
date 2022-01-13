<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\AppStores\GooglePlayStoreProvider;
use App\AppStores\AppleStoreProvider;
use App\Engine\SaveStoreData;

class FetchAppInfo extends Command
{
    /**
     * Fetch details of a specific App
     *
     * @var string
     */
    protected $signature = 'fetch:app
                            {--id=}
                            {--store=}
                            {--save}';
//id for testing:1205990992, 1600880394, 1586321858
// com.pinterest.twa

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
        $save  = $this->option('save');

        $this->info('Fetching Info');

        $provider = ($store == 'apple') ? AppleStoreProvider::class : GooglePlayStoreProvider::class;

        if ($save === true) {
            $provider = SaveStoreData::class;
        }

        $store = resolve($provider, [
            'id'       => $id,
            'language' => 'en',
            'country'  => 'us',
            'store' => $store,
        ]);

        $app = $store->app();

        if ($app) {
            $this->info($app['id'] . '. ' . $app['name']);
        } else {
            $this->error('No App Found');
        }
    }
}
