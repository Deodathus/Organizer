<?php

namespace App\Repository;

use App\Entity\WorktimeEntryEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class WorktimeEntryRepository extends ServiceEntityRepository implements WorktimeEntryRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    /**
     * WorktimeEntryEntityRepository constructor.
     *
     * @param \Doctrine\Persistence\ManagerRegistry $registry
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, WorktimeEntryEntity::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $id
     *
     * @return \App\Entity\WorktimeEntryEntity
     */
    public function fetch(int $id): WorktimeEntryEntity
    {
        return $this->find($id);
    }

    /**
     * @return WorktimeEntryEntity[]
     */
    public function fetchAll(): array
    {
        return $this->createQueryBuilder('worktime_entry_entity')
            ->orderBy('worktime_entry_entity.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param array $dates
     *
     * @return WorktimeEntryEntity[]
     */
    public function fetchByDateIn(array $dates): array
    {
        return $this->createQueryBuilder('worktime_entry_entity')
            ->where('worktime_entry_entity.date IN (:dates)')
            ->orderBy('worktime_entry_entity.date', 'DESC')
            ->setParameter('dates', $dates)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param \App\Entity\WorktimeEntryEntity $entity
     */
    public function store(WorktimeEntryEntity $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->entityManager->flush();
    }

    public function remove(WorktimeEntryEntity $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }
}
