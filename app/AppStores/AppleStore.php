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
use phpDocumentor\Reflection\Types\Integer;

class AppleStore implements StoreInterface
{

    public Client $client;
    public int $id;

    public function __construct(Client $client, int $id)
    {
        $this->client = $client;
        $this->id = $id;
    }

    public function reviews(): bool|Collection
    {

        $url = 'https://itunes.apple.com/us/rss/customerreviews/page=1/id=' . $this->id . '/sortby=mostrecent/json';

        try {
            $response = $this->client->get($url);
            $json = json_decode($response->getBody()->getContents());

            if (!isset($json->feed->entry)) {
                return false;
            }
            $entries = $json->feed->entry;

            return collect($entries)->map(function ($review) {

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


        } catch (GuzzleException $error) {
            return false;
            return $error->getMessage();
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
