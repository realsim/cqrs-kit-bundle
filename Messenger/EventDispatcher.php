<?php

namespace Mechanic\CqrsKit\Messenger;

use Symfony\Component\Messenger\MessageBusInterface;

final class EventDispatcher
{
    private MessageBusInterface $eventBus;

    public function __construct(MessageBusInterface $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    public function emit($event): void
    {
        $this->eventBus->dispatch($event);
    }
}
