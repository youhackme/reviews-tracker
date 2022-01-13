<?php
/**
 * Created by PhpStorm.
 * User: Hyder Bangash
 * Date: 1/8/22
 * Time: 1:22 PM
 */

namespace App\AppStores;

use App\Models\Application;
use App\StoreInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use Symfony\Component\DomCrawler\Crawler;


class AppleStore implements StoreInterface
{

    public Client $client;
    public array $config;

    public function __construct(Client $client, array $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    public function reviews(): bool|Collection
    {

        $url = 'https://itunes.apple.com/' . $this->config['country'] . '/rss/customerreviews/page=1/id=' . $this->config['id'] . '/sortby=mostrecent/json';

        try {
            $response = $this->client->get($url);
            $json     = json_decode($response->getBody()->getContents());

            if (!isset($json->feed->entry)) {
                return false;
            }
            $entries = $json->feed->entry;

            return collect($entries)->map(function ($review) {

                return [
                    'author'      => $review->author->name->label,
                    'url'         => $review->author->uri->label,
                    'reviewed_at' => $review->updated->label,
                    'score'      => (int)$review->{'im:rating'}->label,
                    'version'     => $review->{'im:version'}->label,
                    'id'          => $review->id->label,
                    'title'       => $review->title->label,
                    'description' => $review->content->label,
                    'votes'        => $review->{'im:voteSum'}->label,
                    'country'     => $this->config['country'],
                ];

            });


        }
        catch (GuzzleException $error) {
            return false;
            return $error->getMessage();
        }
    }

    public function ratings()
    {
        $url = 'https://itunes.apple.com/us/customer-reviews/id' . $this->config['id'] . '?displayable-kind=11';

        try {
            $response = $this->client->get($url, [
                'headers' => [
                    'X-Apple-Store-Front' => '143441,12',
                ],
            ]);

            $html = $response->getBody()->getContents();

            $crawler = new Crawler($html);
            $stars   = $crawler->filter('.ratings-histogram > .vote')
                ->extract(['aria-label']);

            if (!empty($stars)) {
                return collect($stars)->map(function ($item) {
                    $data      = explode(',', $item);
                    $star      = trim(str_replace(['stars', 'star'], '', $data[0]));
                    $starValue = (int)trim(str_replace('ratings', '', trim($data[1])));

                    return [
                        $star => $starValue,
                    ];
                });
            }


        }
        catch (GuzzleException $error) {
            return false;
            return $error->getMessage();
        }

    }

    public function app()
    {
        $url = 'https://itunes.apple.com/lookup?id=' . $this->config['id'] . '&country=us&entity=software';

        try {
            $response = $this->client->get($url);
            $json     = json_decode($response->getBody()->getContents());

            if ($json->resultCount === 0) {
                return false;
            }

            $collection = collect($json->results)->map(function ($app) {

                return [
                    'id'                            => $app->trackId,
                    'name'                          => $app->trackName,
                    'screenshots'                   => $app->screenshotUrls,
                    'required_os'                   => $app->minimumOsVersion,
                    'ipad_screenshots'              => $app->ipadScreenshotUrls,
                    'icon'                          => $app->artworkUrl512,
                    'developer_url'                 => $app->artistViewUrl,
                    'languages'                     => $app->languageCodesISO2A,
                    'size'                          => $app->fileSizeBytes,
                    'price'                         => $app->price,
                    'version'                       => $app->version,
                    'current_version_score'         => $app->averageUserRatingForCurrentVersion,
                    'current_version_ratings_count' => $app->userRatingCountForCurrentVersion,
                    'reviews'                       => $app->userRatingCount,
                    'score'                         => $app->averageUserRating,
                    'url'                           => $app->trackViewUrl,
                    'bundle'                        => $app->bundleId,
                    'released_at'                   => $app->releaseDate,
                    'updated_at'                    => $app->currentVersionReleaseDate ?? $app->releaseDate,
                    'developer'                     => $app->artistName,
                    'developer_id'                  => $app->artistId,
                    'genre'                         => $app->primaryGenreName,
                    'genre_id'                      => $app->primaryGenreId,
                    'currency'                      => $app->currency,
                ];

            })->each(function ($item) {
                Application::firstOrCreate(
                    ['applications_id' => $item['id']],
                    [
                        'applications_id' => $item['id'],
                        'name'            => $item['name'],
                        'screenshots'     => $item['screenshots'],
                        'icon'            => $item['icon'],
                        'developer_url'   => $item['developer_url'],
                        'languages'       => $item['languages'],
                        'reviews'         => $item['reviews'],
                        'score'           => $item['score'],
                        'url'             => $item['url'],
                        'released_at'     => $item['released_at'],
                        'developer_id'    => $item['developer_id'],
                        'genre'           => $item['genre'],
                    ]
                );
            });


            return $collection->first();


        }
        catch (GuzzleException $error) {
            return false;
            return $error->getMessage();
        }

    }

    public function search()
    {
        try {
            $url      = 'https://itunes.apple.com/search?term=' . $this->config['term'] . '&entity=software&country=' . $this->config['country'] . '&lang=' . $this->config['language'] . '&limit=' . $this->config['limit'];
            $response = $this->client->get($url);
            $json     = json_decode($response->getBody()->getContents());
            if ($json->resultCount === 0) {
                return false;
            }

            return collect($json->results)->map(function ($app) {

                return [
                    'id'                            => $app->trackId,
                    'name'                          => $app->trackName,
                    'screenshots'                   => $app->screenshotUrls,
                    'required_os'                   => $app->minimumOsVersion,
                    'ipad_screenshots'              => $app->ipadScreenshotUrls,
                    'icon'                          => $app->artworkUrl512,
                    'developer_url'                 => $app->artistViewUrl,
                    'languages'                     => $app->languageCodesISO2A,
                    'size'                          => $app->fileSizeBytes,
                    'price'                         => $app->price,
                    'version'                       => $app->version,
                    'current_version_score'         => $app->averageUserRatingForCurrentVersion,
                    'current_version_ratings_count' => $app->userRatingCountForCurrentVersion,
                    'reviews'                       => $app->userRatingCount,
                    'score'                         => $app->averageUserRating,
                    'url'                           => $app->trackViewUrl,
                    'bundle'                        => $app->bundleId,
                    'released_on'                   => $app->releaseDate,
                    'updated_on'                    => $app->currentVersionReleaseDate ?? $app->releaseDate,
                    'developer'                     => $app->artistName,
                    'developer_id'                  => $app->artistId,
                    'genre'                         => $app->primaryGenreName,
                    'genre_id'                      => $app->primaryGenreId,
                    'currency'                      => $app->currency,


                ];

            });


        }
        catch (GuzzleException $error) {
            return false;
            return $error->getMessage();
        }
    }
}
