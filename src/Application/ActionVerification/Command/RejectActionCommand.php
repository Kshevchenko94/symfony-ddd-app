<?php

namespace App\Application\ActionVerification\Command;

readonly class RejectActionCommand
{
    public function __construct(
        public string $actionId,
        public string $reason
    ) {
    }
}
