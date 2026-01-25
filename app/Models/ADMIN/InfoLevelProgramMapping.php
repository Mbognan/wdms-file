<?php

namespace App\Models\ADMIN;

use Illuminate\Database\Eloquent\Model;

class InfoLevelProgramMapping extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'accreditation_info_id',
        'program_id',
        'level_id',

    ];
    // InfoLevelProgramMapping.php
public function accreditationInfo()
{
    return $this->belongsTo(AccreditationInfo::class, 'accreditation_info_id');
}

public function level()
{
    return $this->belongsTo(AccreditationLevel::class, 'level_id');
}

public function program()
{
    return $this->belongsTo(Program::class, 'program_id');
}
public function programAreas()
{
    return $this->hasMany(ProgramAreaMapping::class, 'info_level_program_mapping_id');
}
}
