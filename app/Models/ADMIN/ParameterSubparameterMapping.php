<?php

namespace App\Models\ADMIN;

use Illuminate\Database\Eloquent\Model;

class ParameterSubparameterMapping extends Model
{
     protected $table = 'parameter_subparameter_mappings';

    public function uploads()
    {
        return $this->hasMany(
            AccreditationDocuments::class,
            'subparameter_id'
        );
    }
}
