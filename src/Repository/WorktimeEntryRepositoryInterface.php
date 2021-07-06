<?php

namespace App\Repository;

use App\Entity\WorktimeEntryEntity;

interface WorktimeEntryRepositoryInterface
{
    /**
     * @param int $id
     *
     * @return \App\Entity\WorktimeEntryEntity
     */
    public function fetch(int $id): WorktimeEntryEntity;

    /**
     * @return WorktimeEntryEntity[]
     */
    public function fetchAll(): array;

    /**
     * @param array $dates
     *
     * @return WorktimeEntryEntity[]
     */
    public function fetchByDateIn(array $dates): array;

    /**
     * @param \App\Entity\WorktimeEntryEntity $entity
     */
    public function store(WorktimeEntryEntity $entity): void;

    /**
     * @return void
     */
    public function update(): void;

    /**
     * @param \App\Entity\WorktimeEntryEntity $entity
     */
    public function remove(WorktimeEntryEntity $entity): void;
}
