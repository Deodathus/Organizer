<?php

namespace App\Service;

use App\DTO\ProjectDto;
use App\Entity\Project;

interface ProjectServiceInterface
{
    /**
     * @param int $id
     *
     * @return \App\Entity\Project
     */
    public function fetch(int $id): Project;

    /**
     * @return \App\Entity\Project[]
     */
    public function fetchAll(): array;

    /**
     * @param \App\DTO\ProjectDto $projectDto
     */
    public function store(ProjectDto  $projectDto): void;
}
