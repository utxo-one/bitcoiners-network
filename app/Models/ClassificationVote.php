<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassificationVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'classifier_id',
        'classified_id',
        'classification_type',
    ];

    public function classifier()
    {
        return $this->belongsTo(User::class, 'classifier_id');
    }

    public function classified()
    {
        return $this->belongsTo(User::class, 'classified_id');
    }
}
