<?php
/**
 * Created by PhpStorm.
 * User: Hyder Bangash
 * Date: 1/8/22
 * Time: 1:22 PM
 */

namespace App\AppStores;

use App\StoreInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;

class AppleStore implements StoreInterface
{

    public Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function reviews()
    {

        $url = 'https://itunes.apple.com/us/rss/customerreviews/page=1/id=1205990992/sortby=mostrecent/json';

        try {
            $response = $this->client->get($url);
            $json = json_decode($response->getBody()->getContents());
            $entries = $json->feed->entry;

            // if (!empty($entries)) {
            $reviews = collect($entries)->map(function ($review) {

                return [
                    'author'      => $review->author->name->label,
                    'url'         => $review->author->uri->label,
                    'updated_on'  => $review->updated->label,
                    'rating'      => (int)$review->{'im:rating'}->label,
                    'version'     => $review->{'im:version'}->label,
                    'id'          => $review->id->label,
                    'title'       => $review->title->label,
                    'description' => $review->content->label,
                    'vote'        => $review->{'im:voteSum'}->label,
                ];

            });

            dd($reviews);


        } catch (GuzzleException $e) {
            dd($e);
        }
    }

    public function ratings()
    {
        // TODO: Implement ratings() method.
    }

    public function search()
    {
        // TODO: Implement search() method.
    }
}
