<?php

namespace Mechanic\CqrsKit\ViewModel;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Mechanic\CqrsKit\Entity\EntityUtils;
use Symfony\Contracts\Service\Attribute\Required;
use LogicException;
use function sprintf;

trait FetcherUtils
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

    private function qb(string $rootAlias, ?string $indexBy = null): QueryBuilder
    {
        return $this->orm
            ->getRepository($this->getEntityName())
            ->createQueryBuilder($rootAlias, $indexBy);
    }

    private function view(string $class, string $rootAlias = null, ?string $indexBy = null): ViewBuilder
    {
        $qb = $this->qb($rootAlias ?: EntityUtils::alias($this->getEntityName()), $indexBy);

        return new ViewBuilder($qb, $class);
    }

    private function hydrateAll(QueryBuilder|Query $query, callable $rowHydrator): array
    {
        if ($query instanceof QueryBuilder) {
            $query = $query->getQuery();
        }

        $result = [];

        foreach ($query->getArrayResult() as $key => $data) {
            $result[$key] = $rowHydrator($data);
        }

        return $result;
    }

    private function hydrateRow(QueryBuilder|Query $query, callable $hydrator, bool $allowNullResult = true): mixed
    {
        if ($query instanceof QueryBuilder) {
            $query = $query->getQuery();
        }

        $data = $query->getOneOrNullResult(Query::HYDRATE_ARRAY);

        if (null === $data) {
            if (true !== $allowNullResult) {
                throw new NoResultException();
            }

            return null;
        }

        return $hydrator($data);
    }
}
