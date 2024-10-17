<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreferenceSaveLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'source',
        'category',
        'author',
        'is_fetched',
        'user_id'
    ];
}
