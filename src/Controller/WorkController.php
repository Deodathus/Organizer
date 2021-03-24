<?php

namespace App\Controller;

use App\DTO\WorktimeDto;
use App\Service\TimeServiceInterface;
use App\Service\WorktimeEntryServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WorkController extends AbstractController
{
    private TimeServiceInterface $timeService;

    private WorktimeEntryServiceInterface $worktimeEntryService;

    public function __construct(TimeServiceInterface $timeService, WorktimeEntryServiceInterface $worktimeEntryService)
    {
        $this->timeService = $timeService;
        $this->worktimeEntryService = $worktimeEntryService;
    }

    public function index(): Response
    {
        return $this->render('work/index.html.twig', [
            'worktimeEntryEntities' => $this->worktimeEntryService->fetchAll(),
            'lastWorktimeEntryEntities' => $this->worktimeEntryService->fetchLast(8),
            'date' => $this->timeService->getCurrentTime(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $worktimeEntryDto = new WorktimeDto(
            $request->get('title'),
            $request->get('description'),
            $request->get('time_amount'),
            $request->get('date')
        );

        $this->worktimeEntryService->store($worktimeEntryDto);

        $this->addFlash('notice', 'entry was successfuly added!');

        return $this->redirectToRoute('work.index');
    }

    public function update(int $id, Request $request): RedirectResponse
    {
        $worktimeEntryDto = new WorktimeDto(
            $request->get('title'),
            $request->get('description'),
            $request->get('time_amount'),
            $request->get('date')
        );

        $this->worktimeEntryService->update($id, $worktimeEntryDto);

        $this->addFlash('notice', 'entry was successfully updated!');

        return $this->redirectToRoute('work.index');
    }

    public function remove(int $id): RedirectResponse
    {
        $this->worktimeEntryService->remove($id);

        $this->addFlash('notice', 'entry was successfuly deleted!');

        return $this->redirectToRoute('work.index');
    }
}
