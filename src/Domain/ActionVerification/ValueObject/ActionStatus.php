<?php

namespace App\Domain\ActionVerification\ValueObject;

enum ActionStatus: string
{
    case PENDING  = 'pending';  //ожидает проверки
    case APPROVED = 'approved'; //одобрено
    case REJECTED = 'rejected'; //отклонено
}
