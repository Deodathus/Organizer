<?php
declare(strict_types=1);

namespace App\Modules\Google\YouTube\Repository;

use App\Modules\Google\YouTube\Entity\ClientConfiguration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ClientConfigurationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClientConfiguration::class);
    }

    public function fetchBySid(string $sid): ?ClientConfiguration
    {
        $queryBuilder = $this->createQueryBuilder('cc');
        $queryBuilder
            ->where('cc.sid = :sid')
            ->setParameter('sid', $sid)
            ->setMaxResults(1);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function fetchCountBySid(string $sid): int
    {
        $queryBuilder = $this->createQueryBuilder('cc');
        $queryBuilder
            ->select('count(cc.id)')
            ->where('cc.sid = :sid')
            ->setParameter('sid', $sid);

        return (int) $queryBuilder->getQuery()->getFirstResult();
    }

    public function store(ClientConfiguration $clientConfiguration): void
    {
        $this->getEntityManager()->persist($clientConfiguration);
        $this->getEntityManager()->flush();
    }
}
