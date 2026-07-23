<?php

namespace App\Domain\ActionVerification\ValueObject;

enum ActionType: string
{
    case MONEY_TRANSFER  = 'money_transfer';
    case EMAIL_CHANGE    = 'email_change';
    case PASSWORD_CHANGE = 'password_change';
}
