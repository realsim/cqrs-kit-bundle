<?php

namespace Mechanic\CqrsKit\Entity;

interface ContainsDomainEventsInterface
{
    public function releaseEvents(): iterable;
}
