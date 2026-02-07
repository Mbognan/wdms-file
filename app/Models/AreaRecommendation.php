<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ADMIN\Area;

class AreaRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id',
        'area_id',
        'recommendation',
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

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }
}
