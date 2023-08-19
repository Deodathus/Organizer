<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Repository;

use App\Modules\Minecraft\Item\Entity\Recipe;
use App\Modules\Minecraft\Item\Search\FilterBus;
use App\Modules\Minecraft\Item\Search\PaginatedResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

final class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function fetch(int $id): ?Recipe
    {
        $queryBuilder = $this->createQueryBuilder('r');
        $queryBuilder->select()
            ->where('r.id = :id')
            ->setParameter('id', $id)
            ->setMaxResults(1);

        try {
            $result = $queryBuilder->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $exception) {
            return null;
        }

        return $result;
    }

    public function fetchByItemIngredientIdPaginated(int $id, FilterBus $filterBus): PaginatedResult
    {
        $queryBuilder = $this->createQueryBuilder('r');
        $queryBuilder
            ->select()
            ->join('r.ingredients', 'ri')
            ->join('ri.items', 'rii')
            ->where('rii.id = :itemId')
            ->setParameter('itemId', $id);

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

    public function fetchByItemResultIdPaginated(int $id, FilterBus $filterBus): PaginatedResult
    {
        $queryBuilder = $this->createQueryBuilder('r');
        $queryBuilder
            ->select()
            ->join('r.results', 'rr')
            ->join('rr.item', 'rri')
            ->where('rri.id = :itemId')
            ->setParameter('itemId', $id);

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

    public function store(Recipe $recipe): void
    {
        $this->getEntityManager()->persist($recipe);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
        $this->getEntityManager()->getUnitOfWork()->clear();
    }
}
