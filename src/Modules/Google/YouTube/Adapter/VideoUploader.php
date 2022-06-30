<?php
declare(strict_types=1);

namespace App\Modules\Google\YouTube\Adapter;

use App\Modules\Google\YouTube\Entity\ClientConfiguration;
use Google\Service\YouTube;

final class VideoUploader implements VideoUploaderInterface
{
    public function __construct(
        private readonly YouTubeClient $youTubeClient
    ) {}

    public function upload(Video $video, ClientConfiguration $clientConfiguration): void
    {
        $client = $this->youTubeClient->getClient($clientConfiguration, $clientConfiguration->getAccessToken());

        $service = new YouTube($client->getClient());

        if (!file_exists($video->getVideoPath())) {
            throw new \RuntimeException(
                sprintf(
                    'File with given path does not exist! Path: %s', $video->getVideoPath()
                )
            );
        }

        $service->videos->insert(
            $video->getPart(),
            $video->getVideo(),
            [
                'data' => file_get_contents($video->getVideoPath()),
                'mimeType' => 'application/octet-stream',
                'uploadType' => 'multipart',
            ]
        );
    }
}
