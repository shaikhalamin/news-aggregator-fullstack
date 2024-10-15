<?php

namespace App\Services\Aggregator;

use App\Factories\Interfaces\NewsApiInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Throwable;

class NytimesApi implements NewsApiInterface
{
    private $sourceConfig;

    public function __construct()
    {
        $this->sourceConfig = config('news_agrregator.sources' . '.' . AggregatorType::NYTIMES_API);
    }

    public function apiDelay()
    {
        return 14;
    }

    public function prepareParams(array $userPreference = [])
    {
        $preferenceParams = [];
        $currentDate = Carbon::now()->format('Y-m-d');
        $oneYearAgoDate = Carbon::now()->subYear()->format('Y-m-d');

        if (!empty($userPreference['metadata']['categories']) || !empty($userPreference['metadata']['authors'])) {

            $params = [
                'page' => 0,
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
                'page' => 0,
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
        $fqUrl = '';

        $filterParams['page'] = 0;

        if (!empty($params['q'])) {
            $filterParams['q'] = $params['q'];
        }

        if (!empty($params['page'])) {
            $filterParams['page'] = intval($params['page']);
        }

        if (!empty($params['startDate'])) {
            $startDate = Carbon::parse($params['startDate']);
            $filterParams['begin_date'] = $startDate->format('Ymd');
        }

        if (!empty($params['endDate'])) {
            $endDate = Carbon::parse($params['endDate']);
            $filterParams['end_date'] = $endDate->format('Ymd');
        }

        if (!empty($params['category'])) {
            $fqUrl .= 'section_name:("' . $params['category'] . '")';
            $filterParams['fq'] = 'section_name:("' . $params['category'] . '")';
        }

        if (!empty($params['author'])) {
            if (!empty($params['category'])) {
                $fqUrl = 'section_name:("' . $params['category'] . '") AND byline:("' . urlencode($params['author']) . '")';
                $filterParams['fq'] = $fqUrl;
            } else {
                $fqUrl .= 'byline:("' . urlencode($params['author']) . '")';
                $filterParams['fq'] = $fqUrl;
            }
        }

        return $filterParams;
    }

    public function all($params = [])
    {
        Log::info('Fetching [NytimesApi]: all api with data started ===> : ');
        try {
            $httpQuery = [
                // 'page' => 0,
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

            Log::error('[NytimesApi]: all api call error  ===> : ' . $th->getMessage());
            return [];
        }
    }

    public function headlines($params = [])
    {
        Log::info('Fetching [NytimesApi]: headlines api with data started ===> : ');
        try {
            $httpQuery = [
                'page' => 1,
                'api-key' => $this->sourceConfig['api_key'],
                ...$this->format($params)
            ];
            $allUrl = $this->sourceConfig['base_uri'] . '/' . $this->sourceConfig['headlines'];
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

            Log::error('[NytimesApi]: headlines api call error  ===> : ' . $th->getMessage());
            return [];
        }
    }

    public static function transform(mixed $article, bool $isTopStories = false, ?int $userId = null)
    {
        $imageUrl = '';
        if (!empty($article['multimedia']) && count($article['multimedia']) > 0) {
            $imageUrl =  'https://www.nytimes.com/' . $article['multimedia'][0]['url'];
        }

        $payload = [
            'title' => $article['headline']['main'],
            'description' =>  null,
            'content' => $article['lead_paragraph'] ?? null,
            'content_html' => null,
            'image_url' => $imageUrl,
            'author' => $article['byline']['original'] ?? null,
            'news_url' => $article['web_url'] ?? null,
            'news_api_url' => null,
            'source' => AggregatorType::NYTIMES_API,
            'is_topstories' => $isTopStories,
            'response_source' => AggregatorType::NYTIMES_API,
            'category' =>  strtolower($article['section_name']),
            'published_at' => Carbon::parse($article['pub_date'], 'UTC')->format("Y-m-d"),
            'user_id' => $userId,
        ];

        return $payload;
    }

    public function transformArray(mixed $responseData, ?int $userId = null)
    {
        $responseList = [];
        $metaData = [];

        if (!empty($responseData) && !empty($responseData['response']['docs'])) {

            $metaData['total'] = $responseData['response']['meta']['hits'];
            $metaData['page'] = $responseData['response']['meta']['offset'] == 0 ? 0 : ($responseData['response']['meta']['offset'] / 10);
            $metaData['perPage'] = 10;
            $metaData['pageToIterate'] = intval(floor(($metaData['total'] / $metaData['perPage']) - $metaData['page']));

            foreach ($responseData['response']['docs'] as $article) {
                $payload = self::transform($article, false, $userId);
                array_push($responseList, $payload);
            }
        }

        return ['meta' => $metaData, 'result' => $responseList];
    }
}
