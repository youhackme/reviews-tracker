<?php

namespace App\Http\Controllers;

use App\AppStores\AppleStoreProvider;
use App\AppStores\GooglePlayStoreProvider;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\Application;

class Subscribe extends Controller
{
    public function save(Request $request)
    {
        $store  = $request->input('store');
        $id     = $request->input('id');
        $status = $request->input('status', 1);

        $provider = ($store == 'apple') ? AppleStoreProvider::class : GooglePlayStoreProvider::class;

        $store = resolve($provider, [
            [
                'id'       => $id,
                'language' => 'en',
                'country'  => 'us',
            ],
        ]);

        try {
            $app         = $store->app();
            $application = Application::where('applications_id', $app['id'])->first();

            Subscription::updateOrCreate(
                ['applications_id' => $application->id],
                [
                    'applications_id' => $application->id,
                    'users_id'        => 1,
                    'status'          => $status,
                ],
            );
            return response()->json([
                'message' => ($status === 1 ? 'Subscribed' : 'Unsubscribed') . ' successfully',
            ], 201);
        }
        catch (\Exception $exception) {
            dd($exception);
            echo $exception->getMessage();
        }
    }
}
