<?php

namespace App\Http\Controllers;

use App\AppStores\AppleStoreProvider;
use App\AppStores\GooglePlayStoreProvider;
use Illuminate\Http\Request;

class Application extends Controller
{

    /**
     * Search App on the app store by keyword
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function lookup(Request $request)
    {
        $term        = $request->input('search.text');
        $language    = $request->input('search.language', 'en');
        $countryCode = $request->input('search.country_code', 'us');
        $store       = $request->input('search.store');

        $provider = ($store == 'apple') ? AppleStoreProvider::class : GooglePlayStoreProvider::class;
        try {
            $store = resolve($provider, [
                [
                    'term'     => $term,
                    'language' => $language,
                    'country'  => $countryCode,
                    'limit'    => 5,
                ],
            ])->search();


            if ($store) {
                return $store
                    ->slice(0, 5)
                    ->map(function ($item) {
                        return [
                            'id'   => $item['id'],
                            'name' => $item['name'],
                            'icon' => $item['icon'],
                        ];
                    })->toArray();
            } else {
                return response()->json([
                    'message' => 'No Result found for searched Keyword',
                ], 404);
            }

        }
        catch (\Exception $error) {
            dd($error);
        }

    }
}
