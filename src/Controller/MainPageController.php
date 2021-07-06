<?php

namespace App\Controller;

use App\Service\TimeServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class MainPageController extends AbstractController
{
    private TimeServiceInterface $timeService;

    public function __construct(TimeServiceInterface $timeService)
    {
        $this->timeService = $timeService;
    }

    public function index(): Response
    {
        return $this->render('index.html.twig', [
            'date' => $this->timeService->getCurrentTime(),
        ]);
    }
}
