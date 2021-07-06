<?php

namespace App\Service;

use App\DTO\ProjectDto;
use App\Entity\ProjectEntity;
use App\Repository\ProjectRepositoryInterface;

class ProjectService implements ProjectServiceInterface
{
    private ProjectRepositoryInterface $projectRepository;

    public function __construct(ProjectRepositoryInterface $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function fetch(int $id): ProjectEntity
    {
        return $this->projectRepository->find($id);
    }

    public function fetchAll(): array
    {
        return $this->projectRepository->fetchAll();
    }

    public function store(ProjectDto $projectDto): void
    {
        $projectEntity = new ProjectEntity($projectDto->getTitle());

        $this->projectRepository->store($projectEntity);
        $this->projectRepository->save();
    }
}
