<?php

namespace App\Models\ADMIN;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreaParameterMapping extends Model
{
    use HasFactory;

    protected $fillable = ['program_area_mapping_id', 'parameter_id'];

    public function subParameters()
    {
        return $this->belongsToMany(
            SubParameter::class,
            'parameter_subparameter_mappings',
            'area_parameter_mapping_id',
            'subparameter_id'
        )->withTimestamps();
    }

    public function programArea()
    {
        return $this->belongsTo(ProgramAreaMapping::class, 'program_area_mapping_id');
    }

    public function parameter()
    {
        return $this->belongsTo(Parameter::class, 'parameter_id');
    }
}
