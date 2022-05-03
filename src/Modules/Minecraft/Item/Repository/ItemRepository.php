<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Repository;

use App\Modules\Minecraft\Item\Entity\Item;
use App\Modules\Minecraft\Item\Exception\CannotFetchItemsException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\QueryException;
use Doctrine\Persistence\ManagerRegistry;

class ItemRepository extends ServiceEntityRepository
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

    public function deleteById(int $id): void
    {
        $queryBuilder = $this->createQueryBuilder('i');
        $queryBuilder->delete()
            ->where('i.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();
    }
}
