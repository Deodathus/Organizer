<?php

namespace App\Repository;

use App\Entity\WorktimeEntryEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class WorktimeEntryEntityRepository extends ServiceEntityRepository
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
     * @param \App\Entity\WorktimeEntryEntity $entryEntity
     */
    public function store(WorktimeEntryEntity $entryEntity): void
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

    public function remove(WorktimeEntryEntity $worktimeEntryEntity): void
    {
        $this->entityManager->remove($worktimeEntryEntity);
        $this->entityManager->flush();
    }

    // /**
    //  * @return WorkTimeEntryEntity[] Returns an array of WorkTimeEntryEntity objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WorkTimeEntryEntity
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
