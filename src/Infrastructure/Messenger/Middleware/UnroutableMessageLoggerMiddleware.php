<?php

namespace App\Infrastructure\Messenger\Middleware;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Throwable;

readonly class UnroutableMessageLoggerMiddleware implements MiddlewareInterface
{
    public function __construct(
        private LoggerInterface $logger
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        try {
            return $stack->next()->handle($envelope, $stack);
        } catch (Throwable $e) {
            $this->logger->error('Message failed to send', [
                'message_class' => get_class($envelope->getMessage()),
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
