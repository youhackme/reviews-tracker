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
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Psr7\Utils;

class GooglePlay implements StoreInterface
{
    public Client $client;
    public string $id;
    public int $numberOfReviewsPerRequest = 100;
    public ?string $paginatedToken;
    public string $language = 'en';
    public string $country = 'us';

    const SORT = [
        'NEWEST'      => 2,
        'RATING'      => 3,
        'HELPFULNESS' => 1,
    ];
    public int $sort = self::SORT['NEWEST'];

    public function __construct(Client $client, array $config)
    {
        $this->client         = $client;
        $this->id             = $config['id'];
        $this->paginatedToken = $config['paginated_token'] ?? null;

    }

    public function reviews(): bool|Collection
    {
        $url         = 'https://play.google.com/_/PlayStoreUi/data/batchexecute?rpcids=qnKhOb&f.sid=-697906427155521722&bl=boq_playuiserver_20190903.08_p0&hl=' . $this->language . '&gl=' . $this->country . '&authuser&soc-app=121&soc-platform=1&soc-device=1&_reqid=1065213';
        $requestType = 'f.req=%5B%5B%5B%22UsvDTd%22%2C%22%5Bnull%2Cnull%2C%5B2%2C' . $this->sort . '%2C%5B' . $this->numberOfReviewsPerRequest . '%2Cnull%2Cnull%5D%2Cnull%2C%5B%5D%5D%2C%5B%5C%22' . $this->id . '%5C%22%2C7%5D%5D%22%2Cnull%2C%22generic%22%5D%5D%5D';
        if ($this->paginatedToken !== null) {
            $requestType = 'f.req=%5B%5B%5B%22UsvDTd%22%2C%22%5Bnull%2Cnull%2C%5B2%2C' . $this->sort . '%2C%5B' . $this->numberOfReviewsPerRequest . '%2Cnull%2C%5C%22' . $this->paginatedToken . '%5C%22%5D%2Cnull%2C%5B%5D%5D%2C%5B%5C%22' . $this->id . '%5C%22%2C7%5D%5D%22%2Cnull%2C%22generic%22%5D%5D%5D';
        }

        try {
            $response = $this->client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8',
                ],
                'body'    => Utils::streamFor($requestType),
            ]);

            $html            = $response->getBody()->getContents();
            $cleanedResponse = json_decode(substr($html, 5));
            $reviews         = json_decode($cleanedResponse[0][2]);
            if ($reviews !== null) {
                return collect($reviews[0])->map(function ($review) {
                    $millisecondsLastDigits = (string)$review[5][1] ?? '000';
                    $reviewDate             = (int)$review[5][0] + (int)substr($millisecondsLastDigits, 0, 3);
                    dd($review);
                    return [
                        'id'          => $review[0],
                        'username'    => $review[1][0],
                        'userimage'   => $review[1][1][3][2],
                        'reviewed_on' => (new \DateTime())->setTimestamp($reviewDate)->format('Y-m-d H:i:s.v'),
                        'score'       => $review[2],
                        'scoreText'   => $review[2],
                        'url'         => 'https://play.google.com/store/apps/details?id=' . $this->id . '&reviewId=' . urlencode($review[0]),
                        'title'       => $review[0],
                        'text'        => $review[4],
                        'replyDate'   => $review[7][2] ?? null,
                        'replyText'   => $review[7][1] ?? null,
                        'version'     => $review[10],
                        'thumbsUp'    => $review[6],
                    ];
                });
            } else {
                throw new Exception('No Data Found After parsing Response');
            }

        }
        catch (GuzzleException $error) {
            dd($error);
            return false;
        }
    }

    public function ratings()
    {
        $this->reviews();
    }

    public function search()
    {

    }
}
