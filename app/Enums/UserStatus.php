<?php

namespace App\Enums;

enum UserStatus: string
{
    case PENDING = 'Pending';
    case ACTIVE = 'Active';
    case SUSPENDED = 'Suspended';
    case INACTIVE = 'Inactive';
}

