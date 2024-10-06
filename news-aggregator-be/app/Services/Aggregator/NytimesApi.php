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

    public function format(array $params = [])
    {
        $filterParams = [];

        if (!empty($params['q'])) {
            $filterParams['q'] = $params['q'];
        }

        if (!empty($params['startDate'])) {
            $filterParams['begin_date'] = $params['startDate'];
        }

        if (!empty($params['endDate'])) {
            $filterParams['end_date'] = $params['endDate'];
        }

        return $filterParams;
    }

    public function all($params = [])
    {
        Log::info('Fetching [NytimesApi]: all api with data started ===> : ');
        try {
            $httpQuery = [
                'page' => 1,
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

    public function transformArray(mixed $responseData)
    {
        $responseList = [];

        if (!empty($responseData) && !empty($responseData['response']['docs'])) {

            foreach ($responseData['response']['docs'] as $article) {
                $payload = self::transform($article, false, null);
                array_push($responseList,$payload);
            }
        }

        return $responseList;
    }
}
