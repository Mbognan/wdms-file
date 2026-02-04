<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaEvaluationFile extends Model
{
     protected $fillable = [
        'area_evaluation_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'uploaded_by',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
