<?php

namespace App\Factories\Interfaces;

interface NewsApiInterface
{
    public function all(array $params = []);
    public function headlines(array $params = []);
    public function format(array $params = []);
    public static function transform(mixed $article, bool $isTopStories =  false, int | null $userId = null);
    public function transformArray(mixed $responseData);
}