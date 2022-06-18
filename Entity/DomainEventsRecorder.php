<?php

namespace Mechanic\CqrsKit\Entity;

trait DomainEventsRecorder
{
    private array $domainEvents = [];

    private function recordEvent($event): void
    {
        $this->domainEvents[] = $event;
    }

    public function releaseEvents(): iterable
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];

        return $events;
    }
}
