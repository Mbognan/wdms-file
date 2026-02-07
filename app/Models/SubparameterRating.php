<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AccreditationEvaluation;
use App\Models\ADMIN\SubParameter;
use App\Models\RatingOptionS;

class SubparameterRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id',
        'subparameter_id',
        'rating_option_id',
        'score',
    ];

    protected $casts = [
        'score' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function evaluation()
    {
        return $this->belongsTo(AccreditationEvaluation::class, 'evaluation_id');
    }

    public function subparameter()
    {
        return $this->belongsTo(SubParameter::class, 'subparameter_id');
    }

    public function ratingOption()
    {
        return $this->belongsTo(RatingOptions::class, 'rating_option_id');
    }
}
