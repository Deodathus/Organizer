<?php

namespace App\Service;

use App\DTO\ProjectDto;
use App\Entity\ProjectEntity;

interface ProjectServiceInterface
{
    /**
     * @param int $id
     *
     * @return \App\Entity\ProjectEntity
     */
    public function fetch(int $id): ProjectEntity;

    /**
     * @return \App\Entity\ProjectEntity[]
     */
    public function fetchAll(): array;

    /**
     * @param \App\DTO\ProjectDto $projectDto
     */
    public function store(ProjectDto  $projectDto): void;
}
