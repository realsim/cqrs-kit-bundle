<?php

namespace Mechanic\CqrsKit\Messenger;

use Symfony\Component\Messenger\MessageBusInterface;

final class CommandDispatcher
{
    private MessageBusInterface $commandBus;
    private HandledResultExtractor $extractor;

    public function __construct(MessageBusInterface $commandBus, HandledResultExtractor $extractor)
    {
        $this->commandBus = $commandBus;
        $this->extractor = $extractor;
    }

    public function execute($command): mixed
    {
        $envelope = $this->commandBus->dispatch($command);

        return $this->extractor->extract($envelope);
    }
}
