<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Repository;

use App\Modules\Minecraft\Item\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function fetch(int $id): Recipe
    {
        $queryBuilder = $this->createQueryBuilder('r');
        $queryBuilder->select()
            ->where('r.id = :id')
            ->setParameter('id', $id)
            ->setMaxResults(1);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}
