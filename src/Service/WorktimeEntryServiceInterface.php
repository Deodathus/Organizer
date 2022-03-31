<?php

namespace App\Service;

use App\DTO\WorktimeDto;
use App\Entity\WorktimeEntry;

interface WorktimeEntryServiceInterface
{
    /**
     * @param int $id
     *
     * @return \App\Entity\WorktimeEntry
     */
    public function fetch(int $id): WorktimeEntry;

    /**
     * @return \App\Entity\WorktimeEntry[]
     */
    public function fetchAll(): array;

    /**
     * @param int $lastDaysAmount
     *
     * @return WorktimeEntry[]
     */
    public function fetchLast(int $lastDaysAmount): array;

    /**
     * @param \App\DTO\WorktimeDto $worktimeDto
     */
    public function store(WorktimeDto $worktimeDto): void;

    /**
     * @param int $id
     * @param \App\DTO\WorktimeDto $worktimeDto
     */
    public function update(int $id, WorktimeDto $worktimeDto): void;

    /**
     * @param int $id
     */
    public function remove(int $id): void;
}
