<?php

namespace Mechanic\CqrsKit\Repository;

use Doctrine\ORM\EntityManager;

final class BulkProcessor
{
    private EntityManager $orm;
    private string $entityClass;
    private int $bulkSize;

    private array $toDetach = [];

    public function __construct(EntityManager $orm, string $entityClass, int $bulkSize)
    {
        $this->orm = $orm;
        $this->entityClass = $entityClass;
        $this->bulkSize = $bulkSize;

        gc_enable();
    }

    public function save(object $entity): void
    {
        if (!is_a($entity, $this->entityClass)) {
            throw new \InvalidArgumentException(sprintf('Bulk processor expects entities of class "%s", given "%s".', $this->entityClass, get_class($entity)));
        }

        $this->orm->persist($entity);
        $this->toDetach[] = $entity;

        $this->flushIfNeeded();
    }

    public function finish(): void
    {
        $this->flushIfNeeded(true);
    }

    public function __destruct()
    {
        $this->finish();
    }

    private function flushIfNeeded(bool $forceFlush = false): void
    {
        static $counter = 0;

        $counter++;
        if ($forceFlush || $counter >= $this->bulkSize) {
            $this->orm->flush();

            while (null !== $entity = array_shift($this->toDetach)) {
                $this->orm->detach($entity);
                unset($entity);
            }
            $this->orm->clear($this->entityClass);
            $this->toDetach = [];
            gc_collect_cycles();

            $counter = 0;
        }
    }
}
