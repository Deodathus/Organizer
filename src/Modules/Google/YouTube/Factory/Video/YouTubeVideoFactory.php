<?php
declare(strict_types=1);

namespace App\Modules\Google\YouTube\Factory\Video;

use App\Modules\Google\YouTube\Adapter\Video;
use Google\Service\YouTube\Video as YouTubeVideo;
use Google\Service\YouTube\VideoSnippet;

final class YouTubeVideoFactory
{
    public static function build(string $path, string $title, array $tags): Video
    {
        $video = new YouTubeVideo();

        $videoSnippet = new VideoSnippet();
        $videoSnippet->setTitle(sprintf('%s | Reddit Videos', $title));
        $videoSnippet->setTags($tags);

        $video->setSnippet($videoSnippet);

        return new Video(
            part: 'snippet',
            videoPath: $path,
            video: $video
        );
    }
}
