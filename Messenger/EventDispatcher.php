<?php

namespace Mechanic\CqrsKit\Messenger;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

final class EventDispatcher
{
    private MessageBusInterface $eventBus;

    public function __construct(MessageBusInterface $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    public function emit($event): void
    {
        $envelope = (new Envelope($event))
            ->with(new DispatchAfterCurrentBusStamp());
        $this->eventBus->dispatch($envelope);
    }
}
