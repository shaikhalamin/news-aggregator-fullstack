<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFeed extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'content',
        'content_html',
        'image_url',
        'author',
        'news_url',
        'news_api_url',
        'source',
        'category',
        'published_at',
        'user_id',
        'response_source',
        'is_topstories'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
