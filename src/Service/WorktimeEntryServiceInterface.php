<?php

namespace App\Service;

use App\DTO\WorktimeDto;
use App\Entity\WorktimeEntry;

interface WorktimeEntryServiceInterface
{
    public function fetch(int $id): WorktimeEntry;

    /**
     * @return WorktimeEntry[]
     */
    public function fetchAll(): array;

    /**
     * @return WorktimeEntry[]
     */
    public function fetchLast(int $lastDaysAmount): array;

    public function store(WorktimeDto $worktimeDto): void;

    public function update(int $id, WorktimeDto $worktimeDto): void;

    public function remove(int $id): void;
}
