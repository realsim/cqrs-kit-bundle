<?php

namespace Mechanic\CqrsKit\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use LogicException;
use Mechanic\CqrsKit\Entity\EntityUtils;
use Symfony\Contracts\Service\Attribute\Required;
use function sprintf;

trait RepositoryUtils
{
    private EntityManager $orm;

    abstract protected function getEntityName(): string;

    #[Required]
    public function initialize(ManagerRegistry $registry): void
    {
        $em = $registry->getManagerForClass($this->getEntityName());
        if (null === $em) {
            throw new LogicException(sprintf('Entity manager for entity "%s" is not defined.', $this->getEntityName()));
        }

        $this->orm = $em;
    }

    private function qb(string $rootAlias = null, ?string $indexBy = null): QueryBuilder
    {
        return $this->repo()
            ->createQueryBuilder($rootAlias ?? EntityUtils::alias($this->getEntityName()), $indexBy);
    }

    private function repo(): EntityRepository
    {
        return $this->orm->getRepository($this->getEntityName());
    }

    private function findOrThrowError($id, $lockMode = null, $lockVersion = null)
    {
        $entity = $this->orm->find($this->getEntityName(), $id, $lockMode, $lockVersion);

        if (null === $entity) {
            throw EntityNotFoundException::fromClassNameAndIdentifier($this->getEntityName(), is_array($id) ? $id : [$id]);
        }

        return $entity;
    }

    private function persistAndFlush($entity): void
    {
        $this->orm->persist($entity);
        $this->orm->flush();
    }

    private function createBulkProcessor(int $bulkSize): BulkProcessor
    {
        return new BulkProcessor($this->orm, $this->getEntityName(), $bulkSize);
    }
}
