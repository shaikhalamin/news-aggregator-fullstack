<?php

namespace App\Services\Aggregator;

use App\Factories\Interfaces\NewsApiInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use jcobhams\NewsApi\NewsApi;
use Throwable;

class NewsApiOrg implements NewsApiInterface
{

    public function __construct(private NewsApi $newsApi) {}

    public function apiDelay()
    {
        return now()->addSeconds(5);
    }

    public function prepareParams(array $userPreference = [])
    {
        $preferenceParams = [];

        if (!empty($userPreference['metadata']['categories'])) {

            $params = [
                'page' => 1,
                'callInit' => true,
            ];

            $categories = $userPreference['metadata']['categories'];
            foreach ($categories as $category) {
                $params = [
                    ...$params,
                    'q' => $category
                ];

                $preferenceParams[] = $params;
            }
        }

        return $preferenceParams;
    }

    public function format(array $params = [])
    {
        $filterParams = [];

        if (!empty($params['q'])) {
            $filterParams['q'] = $params['q'];
        }

        if (!empty($params['startDate'])) {
            $filterParams['from'] = $params['startDate'];
        }

        if (!empty($params['endDate'])) {
            $filterParams['to'] = $params['endDate'];
        }

        return $filterParams;
    }

    public function all($params = [])
    {
        Log::info('Fetching [NewsApiOrg]: all api with data started ===> : ');
        try {
            $q = $params['q'] ?? null;
            $sources = $params['sources'] ?? null;
            $domains  = $params['domains'] ?? null;
            $exclude_domains =  $params['exclude_domains'] ?? null;
            $from = $params['startDate'] ?? null;
            $to = $params['startDate'] ?? null;
            $language = $params['language'] ?? null;
            $sort_by = $params['sort_by'] ?? null;
            $page_size = $params['page_size'] ?? null;
            $page = $params['page'] ?? null;
            return $this->newsApi
                ->getEverything(
                    $q,
                    $sources,
                    $domains,
                    $exclude_domains,
                    $from,
                    $to,
                    $language,
                    $sort_by,
                    $page_size,
                    $page
                );
        } catch (Throwable $th) {

            Log::error('[NewsApiOrg]: all api call error  ===> : ' . $th->getMessage());
            return [];
        }
    }

    public function headlines($params = [])
    {
        Log::info('Fetching [NewsApiOrg]: headlines api with data started ===> : ');
        try {
            $q = $params['q'] ?? null;
            $sources = $params['sources'] ?? null;
            $country = $params['country'] ?? null;
            $category = $params['category'] ?? null;
            $page_size = $params['page_size'] ?? null;
            $page = $params['page'] ?? null;
            return $this->newsApi
                ->getTopHeadlines(
                    $q,
                    $sources,
                    $country,
                    $category,
                    $page_size,
                    $page
                );
        } catch (Throwable $th) {
            Log::error('[GuardianApi]: headlines api call error  ===> : ' . $th->getMessage());
            return [];
        }
    }

    public static function transform(mixed $article, bool $isTopStories = false, ?int $userId = null)
    {
        return [
            'title' => $article->title,
            'description' => $article->description ?? null,
            'content' => $article->content ?? null,
            'content_html' => null,
            'image_url' => $article->urlToImage ?? null,
            'author' => $article->author ?? null,
            'news_url' => $article->url ?? null,
            'news_api_url' => null,
            'source' => AggregatorType::NEWS_API_ORG,
            'is_topstories' => $isTopStories,
            'response_source' => $article->source ? $article->source->id : AggregatorType::NEWS_API_ORG,
            'category' => null,
            'published_at' => Carbon::parse($article->publishedAt, 'UTC')->format("Y-m-d"),
            'user_id' => $userId,
        ];
    }

    public function transformArray(mixed $responseData, ?int $userId = null, ?array $params = [])
    {
        $responseList = [];
        $metaData = [];

        if (!empty($responseData) && $responseData->totalResults > 0) {
            $metaData['total'] = $responseData->totalResults;
            $metaData['page'] = intval($params['page']);
            $metaData['perPage'] = 100;
            $metaData['pageToIterate'] = intval(floor(($metaData['total'] / $metaData['perPage']) - $metaData['page']));

            foreach ($responseData->articles as $article) {
                $payload = self::transform($article, false, $userId);
                array_push($responseList, $payload);
            }
        }

        return ['meta' => $metaData, 'result' => $responseList];
    }
}
