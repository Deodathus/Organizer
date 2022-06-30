<?php
declare(strict_types=1);

namespace App\Modules\Google\YouTube\Adapter;

use Google\Service\YouTube\Video as YouTubeVideo;

final class Video
{
    public function __construct(
        private readonly string $part,
        private readonly string $videoPath,
        private readonly YouTubeVideo $video
    ) {}

    public function getPart(): string
    {
        return $this->part;
    }

    public function getVideoPath(): string
    {
        return $this->videoPath;
    }

    public function getVideo(): YouTubeVideo
    {
        return $this->video;
    }
}
