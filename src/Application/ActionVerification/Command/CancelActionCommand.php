<?php

namespace App\Application\ActionVerification\Command;

readonly class CancelActionCommand
{
    public function __construct(
        public string  $actionId,
        public ?string $reason = null
    ) {
    }
}
