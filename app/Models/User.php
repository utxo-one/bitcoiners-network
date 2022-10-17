<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Enums\UserType;
use Attribute;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    public $incrementing = false;
    protected $primaryKey = 'twitter_id';
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'oauth_token',
        'oauth_token_secret',
        'last_crawled_at',
        'last_processed_at',
        'profile_photo_url',
        'current_team_id',
        'two_factor_confirmed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'twitter_profile_image_url_high_res', 'follows_authenticated_user', 'is_followed_by_authenticated_user',
    ];

    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'followee_id', 'follower_id');
    }

    public function follows()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followee_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id', 'twitter_id');
    }

    public function followRequests()
    {
        return $this->hasMany(FollowRequest::class, 'user_id', 'twitter_id');
    }

    public function tweets()
    {
        return $this->hasMany(Tweet::class, 'user_id', 'twitter_id');
    }

    public function endorsements()
    {
        return $this->hasMany(Endorsement::class, 'endorser_id', 'twitter_id');
    }

    public function endorsementsReceived()
    {
        return $this->hasMany(Endorsement::class, 'endorsee_id', 'twitter_id');
    }

    public function classificationVotesCast()
    {
        return $this->hasMany(ClassificationVote::class, 'classifier_id', 'twitter_id');
    }

    public function classificationVotesReceived()
    {
        return $this->hasMany(ClassificationVote::class, 'classified_id', 'twitter_id');
    }

    public function followChunks()
    {
        return $this->hasMany(FollowChunk::class, 'user_id', 'twitter_id');
    }

    public function getClassificationSummary()
    {
        $votes = $this->classificationVotesReceived()->get();
        $total = $votes->count();
        $bitcoiner = $votes->where('classification_type', 'bitcoiner')->count();
        $shitcoiner = $votes->where('classification_type', 'shitcoiner')->count();
        $nocoiner = $votes->where('classification_type', 'nocoiner')->count();
        return [
            'total' => $total,
            'bitcoiner' => $bitcoiner,
            'shitcoiner' => $shitcoiner,
            'nocoiner' => $nocoiner,
        ];
    }

    public function getAvailableBalance(): int
    {
        $debits = $this->transactions()
            ->where('type', TransactionType::DEBIT)
            ->where('status', TransactionStatus::FINAL)
            ->sum('amount');

        $credits = $this->transactions()
            ->where('type', TransactionType::CREDIT)
            ->where('status', TransactionStatus::FINAL)
            ->sum('amount');

        return $credits - $debits;
    }

    public function getFollowersByType(UserType $userType)
    {
        return $this->followers()->where('type', $userType);
    }

    public function getFollowingByType(UserType $userType)
    {
        return $this->follows()->where('type', $userType);
    }

    public function getAvailableFollows(UserType $userType)
    {
        return User::where('type', $userType)
            ->whereNotIn('twitter_id', $this->getFollowingByType($userType)->pluck('twitter_id'))
            ->where('twitter_id', '!=', $this->twitter_id);
    }

    public function getFollowsAuthenticatedUserAttribute()
    {
        if ( auth()->user() ) {
            return $this->follows()->where('followee_id', auth()->user()->twitter_id)->exists();
        }
    }

    public function getIsFollowedByAuthenticatedUserAttribute()
    {
        if ( auth()->user() ) {
            return $this->followers()->where('follower_id', auth()->user()->twitter_id)->exists();
        }
    }

    public function getFollowingDataAttribute()
    {
        return [
            'bitcoiners' => $this->follows()->where('type', UserType::BITCOINER)->count(),
            'shitcoiners' => $this->follows()->where('type', UserType::SHITCOINER)->count(),
            'nocoiners' => $this->follows()->where('type', UserType::NOCOINER)->count(),
            'total' => $this->follows()->count(),
        ];
    }

    public function getTwitterProfileImageUrlHighResAttribute()
    {
        return str_replace('_normal', '', $this->twitter_profile_image_url);
    }

    public function getFollowerDataAttribute()
    {
        return [
            'bitcoiners' => $this->followers()->where('type', UserType::BITCOINER)->count(),
            'shitcoiners' => $this->followers()->where('type', UserType::SHITCOINER)->count(),
            'nocoiners' => $this->followers()->where('type', UserType::NOCOINER)->count(),
            'total' => $this->followers()->count(),
        ];
    }
}
