<?php

namespace Mechanic\CqrsKit\Controller;

use Mechanic\CqrsKit\Messenger\QueryDispatcher;
use Symfony\Contracts\Service\Attribute\Required;

trait QueryDispatcherTrait
{
    private QueryDispatcher $queryDispatcher;

    #[Required]
    public function setQueryDispatcher(QueryDispatcher $queryDispatcher): void
    {
        $this->queryDispatcher = $queryDispatcher;
    }

    protected function ask($query): mixed
    {
        return $this->queryDispatcher->ask($query);
    }
}
