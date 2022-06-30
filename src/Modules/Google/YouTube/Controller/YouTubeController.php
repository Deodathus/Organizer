<?php
declare(strict_types=1);

namespace App\Modules\Google\YouTube\Controller;

use App\Modules\Google\YouTube\Adapter\YouTubeClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class YouTubeController extends AbstractController
{
    public function __construct(
        private readonly YouTubeClient $client
    ) {}

    public function listChannel(string $id)
    {
        dd($this->client->getChannelWithId($id));
    }

    public function uploadVideo()
    {
        dd($this->client->uploadVideo());
    }
}
