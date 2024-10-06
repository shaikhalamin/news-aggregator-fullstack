<?php

namespace App\Providers;

use App\Services\Aggregator\AggregatorType;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use jcobhams\NewsApi\NewsApi;

class NewsAggregatorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(NewsApi::class, function (Application $app) {
            $newsAggConfig = $app['config']['news_agrregator']['sources'];
            $apiKey = $newsAggConfig[AggregatorType::NEWS_API_ORG]['api_key'];
            return new NewsApi($apiKey);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
