<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowChunk extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'processed_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'twitter_id');
    }

}
