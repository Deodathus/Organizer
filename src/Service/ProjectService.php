<?php

namespace App\Service;

use App\DTO\ProjectDto;
use App\Entity\Project;
use App\Repository\ProjectRepository;

class ProjectService implements ProjectServiceInterface
{
    private ProjectRepository $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function fetch(int $id): Project
    {
        return $this->projectRepository->find($id);
    }

    public function fetchAll(): array
    {
        return $this->projectRepository->fetchAll();
    }

    public function store(ProjectDto $projectDto): void
    {
        $projectEntity = new Project($projectDto->getTitle());

        $this->projectRepository->store($projectEntity);
        $this->projectRepository->save();
    }
}
