<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    use HasFactory;

    protected $casts = [
        'metadata' => 'array',
    ];

    protected $fillable = [
        'source',
        'metadata',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
