<?php

namespace App\Enums;

enum UserType: string
{
    case UNVERIFIED = 'UNVERIFIED USER';
    case ADMIN = 'ADMIN';
    case TASK_FORCE = 'TASK FORCE';
    case TASK_FORCE_CHAIR = 'TASK FORCE CHAIR';
    case INTERNAL_ASSESSOR = 'INTERNAL ASSESSOR';
    case ACCREDITOR = 'ACCREDITOR';
}
