<?php

namespace App\Domain\ActionVerification\ValueObject;

enum ActionType: string
{
    case MONEY_TRANSFER  = 'money_transfer';  // перевод денег
    case EMAIL_CHANGE    = 'email_change';    // смена email
    case PASSWORD_CHANGE = 'password_change'; // смена пароля
}
