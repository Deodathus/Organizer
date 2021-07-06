<?php

namespace App\Controller;

use App\DTO\ProjectDto;
use App\Service\ProjectServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class ProjectController extends AbstractController
{
    private ProjectServiceInterface $projectService;

    public function __construct(ProjectServiceInterface $projectService)
    {
        $this->projectService = $projectService;
    }

    public function store(Request $request): RedirectResponse
    {
        $this->projectService->store(
            new ProjectDto($request->get('title'))
        );

        return $this->redirectToRoute('work.index');
    }
}
