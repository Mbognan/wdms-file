<?php

namespace App\Models\ADMIN;

use App\Models\AreaEvaluation;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ProgramAreaMapping extends Model
{
      protected $fillable = [
        'info_level_program_mapping_id',
        'name',
        'area_id'

    ];
     public function users()
{
    return $this->belongsToMany(
        User::class,
        'accreditation_assignments',
        'area_id',
        'user_id'
    );
}

     public function assignments()
    {
        return $this->hasMany(AccreditationAssignment::class, 'area_id', 'area_id');
    }
    public function area()
    {
        return $this->belongsTo(Area::class);
    }
    public function getAreaNameAttribute()
    {
        return $this->area->area_name ?? 'N/A';
    }
   public function parameters()
{
    return $this->belongsToMany(
        Parameter::class,
        'area_parameter_mappings',
        'program_area_mapping_id',
        'parameter_id'
    )->withTimestamps();
}
    public function areaParameterMappings()
    {
        return $this->hasMany(
            AreaParameterMapping::class,
            'program_area_mapping_id'
        );
    }

   public function evaluations()
{
    return $this->hasMany(AreaEvaluation::class, 'program_area_mapping_id');
}



}
