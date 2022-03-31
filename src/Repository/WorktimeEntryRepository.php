<?php

namespace App\Repository;

use App\Entity\WorktimeEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class WorktimeEntryRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, WorktimeEntry::class);

        $this->entityManager = $entityManager;
    }

    public function fetch(int $id): WorktimeEntry
    {
        return $this->find($id);
    }

    /**
     * @return WorktimeEntry[]
     */
    public function fetchAll(): array
    {
        return $this->createQueryBuilder('we')
            ->orderBy('we.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return WorktimeEntry[]
     */
    public function fetchByDateIn(array $dates): array
    {
        return $this->createQueryBuilder('we')
            ->where('we.date IN (:dates)')
            ->orderBy('we.date', 'DESC')
            ->setParameter('dates', $dates)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param WorktimeEntry $entryEntity
     */
    public function store(WorktimeEntry $entryEntity): void
    {
        $this->entityManager->persist($entryEntity);
        $this->entityManager->flush();
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->entityManager->flush();
    }

    public function remove(WorktimeEntry $worktimeEntryEntity): void
    {
        $this->entityManager->remove($worktimeEntryEntity);
        $this->entityManager->flush();
    }
}
