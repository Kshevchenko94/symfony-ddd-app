<?php

namespace App\Infrastructure\Messenger\Middleware;

use App\Domain\Common\Event\DomainEventInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
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
            $message = $envelope->getMessage();

            if ($message instanceof DomainEventInterface) {
                $envelope = $envelope->with(new AmqpStamp($message->getRoutingKey()));
            }
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
