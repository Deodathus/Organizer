<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

class ProjectRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Project::class);
        $this->entityManager = $entityManager;
    }

    public function fetch(int $id): Project
    {
        return $this->find($id);
    }

    public function fetchAll(): array
    {
        return $this->findAll();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByName(string $name): ?Project
    {
        return $this->createQueryBuilder('project')
            ->where('project.title = :name')
            ->setParameter('name', $name)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function store(Project $projectEntity): void
    {
        $this->entityManager->persist($projectEntity);
    }

    public function save(): void
    {
        $this->entityManager->flush();
    }
}
