<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Models\Notification;
use App\Models\Review;
use App\Models\Application;

class SendNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Notifications Based on Channel Choice';

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

        //Retrieve active subscriptions
        //Retrieve reviews associated with these subscriptions that have never been sent before

        $this->info('Sending Notifications...');


        Subscription::active()->chunk(200, function ($subscription) {
            Subscription::active()->chunk(200, function ($subscription) {
                try {
                    $reviews = Application::find($subscription->first()->application_id)->reviews;
                    foreach ($reviews as $review) {
                        $this->info($review->description);
                    }
                }
                catch (\Exception $exception) {
                    $this->error($exception->getMessage());
                }
            });
        });


    }
}
