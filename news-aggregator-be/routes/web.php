<?php

use App\Factories\NewsApiFactory;
use App\Jobs\FetchUserFeedJob;
use App\Services\Command\UserFeedRefreshService;
use App\Services\Preference\UserPreferenceService;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return ["success" => true, "message" => "News Aggregator Api Works", "data" => null];
});

Route::get('/refresh', function () {
   // $refrsh = (new UserFeedRefreshService())->refresh();

   dispatch(new FetchUserFeedJob(1));
    //dd(config('news_agrregator.sources'));
    return ["success" => true, "message" => "UserFeedRefreshService ", "data" => null];
});


Route::get('/test-news-api', function (UserPreferenceService $userPreferenceService) {
    
    $newsSource = 'news_api_org';
    $userId = 1;

    $userPreferenceByNewsSource = $userPreferenceService->getPreferenceBySource($newsSource, $userId);

    // check user feed table with user id and source and category to verify already processed or not  

    if (!is_null($userPreferenceByNewsSource)) {
        $newsSourceFactory = NewsApiFactory::create($newsSource);
        $userPreferenceParams = $newsSourceFactory->prepareParams($userPreferenceByNewsSource->toArray());
        return $userPreferenceParams;

    }
   
     return ["success" => true, "message" => "UserFeedRefreshService ", "data" => null];
 });


