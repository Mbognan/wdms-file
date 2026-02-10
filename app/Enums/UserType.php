<?php

namespace App\Enums;

enum UserType: string
{
    case UNVERIFIED = 'UNVERIFIED USER';
    case ADMIN = 'ADMIN';
    case DEAN = 'DEAN';
    case TASK_FORCE = 'TASK FORCE';
    case INTERNAL_ASSESSOR = 'INTERNAL ASSESSOR';
    case ACCREDITOR = 'ACCREDITOR';
}
