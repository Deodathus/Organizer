<?php

namespace App\Repository;

use App\Entity\ProjectEntity;

interface ProjectRepositoryInterface
{
    /**
     * @param int $id
     *
     * @return \App\Entity\ProjectEntity
     */
    public function fetch(int $id): ProjectEntity;

    /**
     * @return ProjectEntity[]
     */
    public function fetchAll(): array;

    /**
     * @param string $name
     *
     * @return null|\App\Entity\ProjectEntity
     */
    public function findByName(string $name): ?ProjectEntity;

    /**
     * @param \App\Entity\ProjectEntity $projectEntity
     */
    public function store(ProjectEntity $projectEntity): void;

    /**
     * @return void
     */
    public function save(): void;
}
