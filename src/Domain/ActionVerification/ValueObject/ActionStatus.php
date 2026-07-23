<?php

namespace App\Domain\ActionVerification\ValueObject;

enum ActionStatus: string
{
    case PENDING  = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
}
