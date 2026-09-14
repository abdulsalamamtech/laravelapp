<?php

namespace App\Enums;

enum AppRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';
    case USER = 'user';
    case CLIENT = 'client';

}
