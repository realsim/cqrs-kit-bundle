<?php

namespace Mechanic\CqrsKit\Entity;

trait DomainEventsRecorder
{
    private array $domainEvents = [];

    /**
     * Для отложенной инициализации событий их можно обернуть в callable.
     * В таком случае инициализация события будет выполнена при вызове метода releaseEvents.
     */
    private function recordEvent($event): void
    {
        $this->domainEvents[] = $event;
    }

    public function clearEvents(): void
    {
        $this->domainEvents = [];
    }

    public function releaseEvents(): iterable
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];

        foreach ($events as $event) {
            if (is_callable($event)) {
                $event = $event($this);
            }

            yield $event;
        }
    }
}
