<?php

namespace App\Models\ADMIN;

use Illuminate\Database\Eloquent\Model;

class AccreditationBody extends Model
{
      protected $fillable = ['name', 'description'];

    public function accreditations()
    {
        return $this->hasMany(AccreditationInfo::class);
    }
}
