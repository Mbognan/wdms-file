<?php

namespace App\Models\ADMIN;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
       protected $fillable = [
        'area_name',
        'area_description',
        'level_id',
        'evaluated'
    ];

    public function programs()
    {
        return $this->belongsToMany(
            Program::class,
            'program_area_mappings'
        );
    }
     public function level()
    {
        return $this->belongsTo(AccreditationLevel::class, 'level_id');
    }
}
