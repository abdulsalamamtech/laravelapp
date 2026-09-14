<?php

namespace App\Enums;

enum EmailChangeCancelledReason: string
{
    case OWNER_CANCEL = 'owner_cancel';
    case SUSPICIOUS_REPORT = 'suspicious_report';
}
