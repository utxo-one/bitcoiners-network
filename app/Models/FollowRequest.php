<?php

namespace App\Models;

use Carbon\Carbon;
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

    protected $appends = [
        'completed_at_for_humans',
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

    public function getCompletedAtForHumansAttribute()
    {
        return Carbon::parse($this->completed_at)->diffForHumans();
    }
}
