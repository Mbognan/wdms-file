<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatingOptions extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'min_score',
        'max_score',
        'applicable',
    ];

    protected $casts = [
        'min_score' => 'integer',
        'max_score' => 'integer',
        'applicable' => 'boolean',
    ];
}
