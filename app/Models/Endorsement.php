<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Endorsement extends Model
{
    use HasFactory;

    protected $fillable = [
        'endorser_id',
        'endorsee_id',
        'endorsement_type',
    ];

    public function endorser()
    {
        return $this->belongsTo(User::class, 'endorser_id');
    }

    public function endorsee()
    {
        return $this->belongsTo(User::class, 'endorsee_id');
    }
}
