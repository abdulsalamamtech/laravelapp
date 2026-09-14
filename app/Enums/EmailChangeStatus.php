<?php

namespace App\Enums;

enum EmailChangeStatus: string
{
    case PENDING = 'pending';
    case SCHEDULED = 'scheduled';
    case APPLIED = 'applied';
    case CANCELLED = 'cancelled';
    case EXPIRED = 'expired';
}
