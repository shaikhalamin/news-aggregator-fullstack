<?php

namespace App\Services\Aggregator;

use App\Factories\Interfaces\NewsApiInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Throwable;

class GuardianApi implements NewsApiInterface
{
    private $sourceConfig;

    public function __construct()
    {
        $this->sourceConfig = config('news_agrregator.sources' . '.' . AggregatorType::GURDIAN_API);
    }

    public function apiDelay()
    {
        return now()->addSeconds(5);
    }

    public function prepareParams(array $userPreference = [])
    {
        $preferenceParams = [];
        $currentDate = Carbon::now()->format('Y-m-d');
        $oneYearAgoDate = Carbon::now()->subYear()->format('Y-m-d');

        if (!empty($userPreference['metadata']['categories']) || !empty($userPreference['metadata']['authors'])) {

            $params = [
                'page' => 1,
                'callInit' => true,
                'startDate' => $oneYearAgoDate,
                'endDate' => $currentDate,
            ];

            if (!empty($userPreference['metadata']['categories']) && empty($userPreference['metadata']['authors'])) {
                $categories = $userPreference['metadata']['categories'];
                foreach ($categories as $category) {
                    $params = [
                        ...$params,
                        'category' => $category
                    ];

                    $preferenceParams[] = $params;
                }
            }

            if (!empty($userPreference['metadata']['authors']) && empty($userPreference['metadata']['categories'])) {
                $authors = $userPreference['metadata']['authors'];
                foreach ($authors as $author) {
                    $params = [
                        ...$params,
                        'author' => $author
                    ];

                    $preferenceParams[] = $params;
                }
            }

            if (!empty($userPreference['metadata']['authors']) && !empty($userPreference['metadata']['categories'])) {
                $categories = $userPreference['metadata']['categories'];
                $authors = $userPreference['metadata']['authors'];
                foreach ($categories as $category) {
                    $params = [
                        ...$params,
                        'category' => $category
                    ];

                    foreach ($authors as $author) {
                        $params = [
                            ...$params,
                            'author' => $author
                        ];

                        $preferenceParams[] = $params;
                    }
                }
            }
        } else {

            $params = [
                'page' => 1,
                'callInit' => true,
                'startDate' => $oneYearAgoDate,
                'endDate' => $currentDate,
            ];

            $preferenceParams[] = $params;
        }

        return $preferenceParams;
    }



    public function format(array $params = [])
    {
        $filterParams = [];

        $filterParams['page'] = 1;

        if (!empty($params['page'])) {
            $filterParams['page'] = intval($params['page']);
        }

        if (!empty($params['q'])) {
            $filterParams['q'] = $params['q'];
        }

        if (!empty($params['startDate'])) {
            $filterParams['from-date'] = $params['startDate'];
        }

        if (!empty($params['endDate'])) {
            $filterParams['to-date'] = $params['endDate'];
        }

        if (!empty($params['category'])) {
            $filterParams['section'] = $params['category'];
        }

        if (!empty($params['author'])) {
            $filterParams['author'] = $params['author'];
        }

        return $filterParams;
    }

    public function all($params = [])
    {
        Log::info('[GuardianApi]: all api call started  ===> : ', $params);
        try {
            $httpQuery = [
                // 'page' => 1,
                'page-size' => 200,
                'show-fields' => 'body,thumbnail,byline,publication,shortUrl,lastModified',
                'api-key' => $this->sourceConfig['api_key'],
                ...$this->format($params)
            ];
            $allUrl = $this->sourceConfig['base_uri'] . '/' . $this->sourceConfig['all'];
            $response = Http::retry(3, 15000)->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->get($allUrl, [...$httpQuery]);

            if ($response->successful()) {
                $data = $response->json();
                return $data;
            } else {
                return [];
            }
        } catch (Throwable $th) {

            Log::error('[GuardianApi]: all api call error  ===> : ' . $th->getMessage());
            return [];
        }
    }


    public function headlines($params = [])
    {
        Log::info('[GuardianApi]: call started  ===> : ');
        try {
            $httpQuery = [
                'page' => 1,
                'page-size' => 200,
                'show-fields' => 'body,thumbnail,byline,publication,shortUrl,lastModified',
                'api-key' => $this->sourceConfig['api_key'],
                ...$this->format($params)
            ];
            $allUrl = $this->sourceConfig['base_uri'] . '/' . $this->sourceConfig['all'];
            $response = Http::retry(3, 200)->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->get($allUrl, [...$httpQuery]);

            if ($response->successful()) {
                $data = $response->json();
                return $data;
            } else {
                return [];
            }
        } catch (Throwable $th) {
            Log::error('[GuardianApi]: headlines api call error  ===> : ' . $th->getMessage());
            return [];
        }
    }

    public static function transform(mixed $article, bool $isTopStories =  false, ?int $userId = null, ?array $params = [])
    {
        return [
            'title' => $article['webTitle'],
            'description' =>  null,
            'content' => $article['fields']['body'],
            'content_html' => null,
            'image_url' => $article['fields']['thumbnail'] ?? null,
            'author' => $article['fields']['byline'] ?? null,
            'news_url' => $article['webUrl'] ?? null,
            'news_api_url' => $article['apiUrl'] ?? null,
            'source' => AggregatorType::GURDIAN_API,
            'is_topstories' => $isTopStories,
            'response_source' => AggregatorType::GURDIAN_API,
            'category' => $article['sectionId'],
            'published_at' => Carbon::parse($article['webPublicationDate'], 'UTC')->format("Y-m-d"),
            'user_id' => $userId,
        ];
    }

    public function transformArray(mixed $responseData, ?int $userId = null, ?array $params = [])
    {
        $responseList = [];
        $metaData = [];

        if (!empty($responseData) && !empty($responseData['response']['results'])) {

            $metaData['total'] = $responseData['response']['total'];
            $metaData['page'] = $responseData['response']['currentPage'];
            $metaData['perPage'] = $responseData['response']['pageSize'];
            $metaData['pageToIterate'] = intval(ceil(($metaData['total'] / $metaData['perPage']) - $metaData['page']));

            foreach ($responseData['response']['results'] as $article) {
                $payload = self::transform($article, false, $userId);
                array_push($responseList, $payload);
            }
        }

        return ['meta' => $metaData, 'result' => $responseList];
    }
}
