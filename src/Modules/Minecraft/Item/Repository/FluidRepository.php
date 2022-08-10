<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Repository;

use App\Modules\Minecraft\Item\Entity\Fluid;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class FluidRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fluid::class);
    }
}
