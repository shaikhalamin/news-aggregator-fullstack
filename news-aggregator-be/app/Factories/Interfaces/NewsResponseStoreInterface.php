<?php

namespace App\Factories\Interfaces;

interface NewsResponseStoreInterface
{
    public function store(int $userId, mixed $responseData, bool $isTopStories = false);
}
