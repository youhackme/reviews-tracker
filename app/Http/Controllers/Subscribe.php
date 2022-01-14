<?php

namespace App\Http\Controllers;

use App\AppStores\AppleStoreProvider;
use App\AppStores\GooglePlayStoreProvider;
use App\Engine\SaveStoreData;
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

        $provider = SaveStoreData::class;

        $store = resolve($provider, [
            'id'       => $id,
            'language' => 'en',
            'country'  => 'us',
            'store'    => $store,
        ]);

        try {
            $app = $store->app();

            if ($app) {

                $application = Application::where('application_id', $app['id'])->first();

                Subscription::updateOrCreate(
                    ['application_id' => $application->id],
                    [
                        'application_id' => $application->id,
                        'user_id'        => 1,
                        'status'          => $status,
                    ],
                );
                return response()->json([
                    'message' => ($status === 1 ? 'Subscribed' : 'Unsubscribed') . ' successfully',
                ], 201);

            } else {
                return response()->json([
                    'message' => 'The application data could not be retrieved.',
                ], 503);
            }

        }
        catch (\Exception $exception) {
            dd($exception);
            echo $exception->getMessage();
        }
    }
}
