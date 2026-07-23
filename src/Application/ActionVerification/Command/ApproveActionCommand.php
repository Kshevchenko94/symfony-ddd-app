<?php

namespace App\Application\ActionVerification\Command;

readonly class ApproveActionCommand
{
    public function __construct(
        public string $actionId,
    ) {
    }
}
