<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramFinalVerdict extends Model
{
        protected $fillable = [
        'program_id',
        'accred_info_id',

        'decided_by',
        'status',
        'revisit_until',

        'comments',
        'finalized_at',
    ];


}
