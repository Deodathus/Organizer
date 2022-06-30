<?php
declare(strict_types=1);

namespace App\Modules\Google\YouTube\Repository;

use App\Modules\Google\YouTube\Entity\ClientAccessToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ClientAccessTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClientAccessToken::class);
    }

    public function store(ClientAccessToken $clientAccessToken): void
    {
        $this->getEntityManager()->persist($clientAccessToken);
        $this->getEntityManager()->flush();
    }
}
