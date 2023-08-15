<?php

namespace App\Controller;

use App\Service\LinkServiceInterface;
use App\Service\TimeServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class MainPageController extends AbstractController
{
    public function __construct(private TimeServiceInterface $timeService, private LinkServiceInterface $linkService)
    {
    }

    public function index(): Response
    {
        return $this->render('index.html.twig', [
            'date' => $this->timeService->getCurrentTime(),
            'links' => $this->linkService->fetchAll(),
        ]);
    }
}
