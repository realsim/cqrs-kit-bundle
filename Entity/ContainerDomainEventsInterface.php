<?php

namespace Mechanic\CqrsKit\Entity;

interface ContainerDomainEventsInterface
{
    public function releaseEvents(): iterable;
}
