<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    use HasFactory;

    protected $fillable = [
        'follower_id',
        'followee_id',
    ];

    public function follower()
    {
        return $this->belongsTo(User::class, 'follower_id', 'twitter_id');
    }

    public function followee()
    {
        return $this->belongsTo(User::class, 'followee_id', 'twitter_id');
    }
}
