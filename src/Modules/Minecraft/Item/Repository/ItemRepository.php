<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Repository;

use App\Modules\Minecraft\Item\Entity\Item;
use App\Modules\Minecraft\Item\Exception\CannotFetchItemsException;
use App\Modules\Minecraft\Item\Search\FilterBus;
use App\Modules\Minecraft\Item\Search\PaginatedResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

final class ItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Item::class);
    }

    /**
     * @param int[] $ids
     *
     * @throws CannotFetchItemsException
     */
    public function fetchByIds(array $ids): array
    {
        $queryBuilder = $this->createQueryBuilder('i');
        $queryBuilder->select()
            ->where('i.id in (:ids)')
            ->setParameter('ids', $ids);

        try {
            $queryBuilder->indexBy('i', 'i.id');
        } catch (QueryException $exception) {
            throw CannotFetchItemsException::fromException($exception);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @throws CannotFetchItemsException
     */
    public function fetchAll(): array
    {
        $queryBuilder = $this->createQueryBuilder('i');

        $queryBuilder->select();

        try {
            $queryBuilder->indexBy('i', 'i.id');
        } catch (QueryException $exception) {
            throw CannotFetchItemsException::fromException($exception);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function fetchAllPaginated(FilterBus $filterBus): PaginatedResult
    {
        $queryBuilder = $this->createQueryBuilder('i');
        $queryBuilder->select();
        try {
            $queryBuilder->indexBy('i', 'i.id');
        } catch (QueryException $exception) {
            throw CannotFetchItemsException::fromException($exception);
        }

        if ($filterBus->getSearchPhrase()) {
            $queryBuilder->where(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->like('i.key', ':searchPhrase'),
                    $queryBuilder->expr()->like('i.subKey', ':searchPhrase'),
                    $queryBuilder->expr()->like('i.name', ':searchPhrase')
                )
            );

            $queryBuilder->setParameter('searchPhrase', '%' . $filterBus->getSearchPhrase() . '%');
        }

        $queryBuilder
            ->setFirstResult($filterBus->getPage() * $filterBus->getPerPage() - $filterBus->getPerPage())
            ->setMaxResults($filterBus->getPerPage());

        $paginator = new Paginator($queryBuilder->getQuery());
        $totalCount = count($paginator);

        return new PaginatedResult(
            $paginator,
            (int) ceil($totalCount / $filterBus->getPerPage()),
            $totalCount
        );
    }

    public function deleteById(int $id): void
    {
        $queryBuilder = $this->createQueryBuilder('i');
        $queryBuilder->delete()
            ->where('i.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();
    }

    public function store(Item $item): void
    {
        $this->getEntityManager()->persist($item);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
