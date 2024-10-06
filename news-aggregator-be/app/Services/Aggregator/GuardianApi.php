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

    public function format(array $params = [])
    {
        $filterParams = [];

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

        return $filterParams;
    }

    public function all($params = [])
    {
        Log::info('[GuardianApi]: all api call started  ===> : ');
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

    public static function transform(mixed $article, bool $isTopStories =  false, ?int $userId = null)
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

    public function transformArray(mixed $responseData)
    {
        $responseList = [];

        if (!empty($responseData) && !empty($responseData['response']['results'])) {

            foreach ($responseData['response']['results'] as $article) {
                $payload = self::transform($article, false, null);
                array_push($responseList,$payload);
            }
        }

        return $responseList;
    }
}
