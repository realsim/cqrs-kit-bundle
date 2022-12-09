<?php

namespace Mechanic\CqrsKit\Entity;

trait DomainEventsRecorder
{
    private array $domainEvents = [];

    /**
     * Для отложенной инициализации событий их можно обернуть в callable.
     * В таком случае инициализация события будет выполнена при вызове метода releaseEvents.
     *
     * @param bool $recordOnce Не записывать одинаковые повторяющиеся события несколько раз
     */
    private function recordEvent($event, bool $recordOnce = false): void
    {
        if (true === $recordOnce) {
            if (in_array($event, $this->domainEvents, true)) {
                return;
            }
        }

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
