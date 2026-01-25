<?php

namespace App\Models\ADMIN;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AccreditationDocuments extends Model
{
   protected $fillable = [
    'subparameter_id',
    'file_name',
    'file_path',
    'file_type',
    'upload_by',
    'accred_info_id',
    'level_id',
    'program_id',
    'area_id',
    'parameter_id',
];
public function uploader()
    {
        return $this->belongsTo(User::class, 'upload_by');
    }

}
