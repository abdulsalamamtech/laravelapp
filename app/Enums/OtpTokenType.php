<?php

namespace App\Enums;

enum OtpTokenType: string
{
    case ACCOUNT_VERIFICATION = 'account-verification';
    case RESET_PASSWORD = 'reset-password';
    case FORGET_PASSWORD = 'forget-password';
    case TWO_FACTOR_AUTHENTICATION = 'two-factor-authentication';
    case EMAIL_CHANGE_OLD = 'email-change-old';
    case EMAIL_CHANGE_NEW = 'email-change-new';
    case EMAIL_CHANGE_ACCEPT = 'email-change-accept';
    case EMAIL_CHANGE_REPORT = 'email-change-report';
}
