<?php

namespace Mechanic\CqrsKit\ViewModel;

use Doctrine\ORM\QueryBuilder;
use function sprintf;

final class ViewBuilder
{
    private QueryBuilder $qb;
    private string $class;
    private array $ctorArguments = [];

    public function __construct(QueryBuilder $qb, string $class)
    {
        $this->qb = $qb;
        $this->class = $class;
    }

    public function args(string ...$args): self
    {
        foreach ($args as $arg) {
            $this->ctorArguments[] = $arg;
        }

        return $this;
    }

    public function createQueryBuilder(): QueryBuilder
    {
        $ctor = sprintf('NEW %s(%s)', $this->class, implode(',', $this->ctorArguments));

        return $this->qb->select($ctor);
    }
}
