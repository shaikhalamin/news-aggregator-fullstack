<?php

namespace App\Factories;

use App\Factories\Interfaces\NewsApiInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use Exception;

class NewsApiFactory
{
    public static function create(string $source): NewsApiInterface | Exception
    {
        $sourceClass = 'App\\Services\\Aggregator\\' . Str::studly($source);

        if (!class_exists($sourceClass)) {
            throw new Exception('News Api source class of  : ' . $source . ' not found ');
        }

        return App::make($sourceClass);
    }
}
