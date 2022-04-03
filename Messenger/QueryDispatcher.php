<?php

namespace Mechanic\CqrsKit\Messenger;

use Symfony\Component\Messenger\MessageBusInterface;

final class QueryDispatcher
{
    private MessageBusInterface $queryBus;
    private HandledResultExtractor $extractor;

    public function __construct(MessageBusInterface $queryBus, HandledResultExtractor $extractor)
    {
        $this->queryBus = $queryBus;
        $this->extractor = $extractor;
    }

    public function ask($query): mixed
    {
        $envelope = $this->queryBus->dispatch($query);

        return $this->extractor->extract($envelope);
    }
}
