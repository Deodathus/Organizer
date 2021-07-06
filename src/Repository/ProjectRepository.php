<?php

namespace App\Repository;

use App\Entity\ProjectEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProjectEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectEntity[]    findAll()
 * @method ProjectEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository implements ProjectRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, ProjectEntity::class);
        $this->entityManager = $entityManager;
    }

    public function fetch(int $id): ProjectEntity
    {
        return $this->find($id);
    }

    public function fetchAll(): array
    {
        return $this->findAll();
    }

    public function findByName(string $name): ?ProjectEntity
    {
        return $this->createQueryBuilder('project')
            ->where('project.title = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function store(ProjectEntity $projectEntity): void
    {
        $this->entityManager->persist($projectEntity);
    }

    public function save(): void
    {
        $this->entityManager->flush();
    }
}
