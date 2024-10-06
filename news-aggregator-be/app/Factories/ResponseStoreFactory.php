<?php

namespace App\Factories;

use App\Factories\Interfaces\NewsResponseStoreInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use Exception;

class ResponseStoreFactory
{
    public static function create(string $source): NewsResponseStoreInterface | Exception
    {
        $transformerClass = 'App\\Services\\ResponseStore\\' . Str::studly($source);

        if (!class_exists($transformerClass)) {
            throw new Exception('ResponseStoreFactory class of  : ' . $source . ' not found ');
        }

        return App::make($transformerClass);
    }
}