<?php

namespace App\Models\ADMIN;

use Illuminate\Database\Eloquent\Model;

class AccreditationLevel extends Model
{
    protected $fillable = ['level_name', 'level_description'];

    public function programs()
    {
        return $this->belongsToMany(
            Program::class,
            'accreditation_level_program'
        );
    }
}
