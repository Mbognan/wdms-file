<?php

namespace App\Models\ADMIN;

use App\Models\ProgramFinalVerdict;
use Illuminate\Database\Eloquent\Model;

class AccreditationInfo extends Model
{
     protected $fillable = [
        'title',
        'year',
        'status',
        'accreditation_body_id',
        'accreditation_date'
    ];
// App\Models\AccreditationInfo.php

public function finalVerdicts()
{
    return $this->hasMany(ProgramFinalVerdict::class, 'accred_info_id');
}

   public function accreditationBody()
{
    return $this->belongsTo(AccreditationBody::class, 'accreditation_body_id');
}


    public function levels()
    {
        return $this->belongsToMany(
            AccreditationLevel::class,
            'accreditation_info_level'
        );
    }
}
