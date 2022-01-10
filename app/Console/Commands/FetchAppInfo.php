<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\AppStores\AppleStore;

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
        $this->info('Fetching Info');

        $store = resolve(AppleStore::class, [
            'id' => (int)$this->option('id'),
        ]);

        $apps = $store->search();

        if ($apps) {
            $apps->each(function ($app, $key) {
                $position = $key + 1;
                $this->info($position . '. ' . $app['name']);
            });
        } else {
            $this->error('No App Found');
        }
    }
}
