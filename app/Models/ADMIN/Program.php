<?php

namespace App\Models\ADMIN;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
      protected $fillable = [
        'program_name',
        'program_description',
        'specialization'
    ];
}
