<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'follow_id',
        'status',
        'transaction_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'twitter_id');
    }

    public function follow()
    {
        return $this->belongsTo(User::class, 'follow_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
