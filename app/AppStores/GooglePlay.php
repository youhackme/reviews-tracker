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
        $url = 'https://play.google.com/store/apps/details?id=' . $this->id . '&hl=' . $this->language . '&gl=' . $this->country;
        try {
            $response = $this->client->get($url);
            $html     = $response->getBody()->getContents();

            preg_match_all('/>AF_initDataCallback[\s\S]*?<\/script/', $html, $matches, PREG_PATTERN_ORDER);

            $matches = current($matches);
            if (!empty($matches)) {


                $result = collect($matches)->filter(function ($match) {

                    preg_match_all("/(ds:.*?)'/", $match, $keyMatch, PREG_PATTERN_ORDER);
                    preg_match_all("/data:([\s\S]*?), sideChannel: {}}\);<\//", $match, $valueMatch,
                        PREG_PATTERN_ORDER);

                    if (isset($keyMatch[1][0]) && isset($valueMatch[1][0])) {
                        return true;
                    }
                    return false;
                })->mapWithKeys(function ($match) use ($html) {

                    preg_match_all("/(ds:.*?)'/", $match, $keyMatch, PREG_PATTERN_ORDER);
                    preg_match_all("/data:([\s\S]*?), sideChannel: {}}\);<\//", $match, $valueMatch,
                        PREG_PATTERN_ORDER);

                    $key   = $keyMatch[1][0];
                    $value = json_decode($valueMatch[1][0]);

                    return [
                        $key => $value,
                    ];


                });

                $emailAddress = $result['ds:5'][0][12][5][4][0] ?? $result['ds:5'][0][12][5][2][0] ?? null;

                return collect([
                    [
                        'name'                => $result['ds:5'][0][0][0],
                        'description'         => $result['ds:5'][0][10][0][1],
                        'descriptionHTML'     => $result['ds:5'][0][10][0][1],
                        'summary'             => $result['ds:5'][0][10][1][1],
                        'installs'            => $result['ds:5'][0][12][9][0],
                        'minInstalls'         => $result['ds:5'][0][12][9][1],
                        'maxInstalls'         => $result['ds:5'][0][12][9][2],
                        'score'               => $result['ds:6'][0][6][0][1],
                        'scoreText'           => $result['ds:6'][0][6][0][0],
                        'ratings'             => $result['ds:6'][0][6][2][1],
                        'reviews'             => $result['ds:6'][0][6][3][1],
                        'histogram'           => $this->buildHistogram($result['ds:6'][0][6][1]),
                        'price'               => $result ['ds:3'][0][2][0][0][0][1][0][0],
                        'free'                => ($result ['ds:3'][0][2][0][0][0][1][0][0]) === 0,
                        'currency'            => $result['ds:3'][0][2][0][0][0][1][0][1],
                        'priceText'           => $result ['ds:3'][0][2][0][0][0][1][0][2],
                        'available'           => $result ['ds:5'][0][12][11][0],
                        'offersIAP'           => !(($result['ds:5'][0][12][12] == null)),
                        'size'                => $result['ds:8'][0],
                        'androidVersion'      => $result ['ds:8'][2],
                        'androidVersionText'  => $result['ds:8'][2],
                        'developer'           => $result['ds:5'][0][12][5][1],
                        'developerId'         => $result ['ds:5'][0][12][5][5][4][2],
                        'developerEmail'      => $result ['ds:5'][0][12][5][2][0],
                        'developerWebsite'    => $result['ds:5'][0][12][5][3][5][2],
                        'developerAddress'    => $emailAddress,
                        'privacyPolicy'       => $result['ds:5'][0][12][7][2],
                        'developerInternalID' => $result['ds:5'][0][12][5][0][0],
                        'genre'               => $result['ds:5'][0][12][13][0][0],
                        'icon'                => $result['ds:5'][0][12][1][3][2],
                        'screenshots'         => $this->screenshots($result['ds:5'][0][12][0]),
                        'updated'             => (new \DateTime())->setTimestamp($result ['ds:5'][0][12][8][0])->format('Y-m-d H:i:s.v'),
                        'version'             => $result['ds:8'][1],
                        'recentChanges'       => $result['ds:5'][0][12][6][1],
                        'features'            => $result ['ds:5'][0][12][16],
                    ],
                ]);

            }

            return false;

        }
        catch (GuzzleException $error) {
            dd($error);
            return false;
        }
    }

    private function screenshots($data)
    {

        if ($data === null) {
            return [];
        }

        return collect($data)->map(function ($item) {
            return $item[3][2];
        });

    }

    private function buildHistogram($data)
    {
        if (!$data) {
            return [
                1 => 0,
                2 => 0,
                3 => 0,
                4 => 0,
                5 => 0,
            ];
        }
        return [
            1 => $data[1][1],
            2 => $data[2][1],
            3 => $data[3][1],
            4 => $data[4][1],
            5 => $data[5][1],
        ];

    }
}
