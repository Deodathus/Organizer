<?php
declare(strict_types=1);

namespace App\Modules\Google\YouTube\Service;

use App\Modules\Google\YouTube\Adapter\VideoUploaderInterface;
use App\Modules\Google\YouTube\Entity\ClientConfiguration;
use App\Modules\Google\YouTube\Exception\VideoException;
use App\Modules\Google\YouTube\Factory\Video\YouTubeVideoFactory;

final class VideoUploader
{
    private const VALID_EXTENSIONS = [
        'mp4',
    ];

    public function __construct(
        private readonly VideoUploaderInterface $videoService
    ) {}

    public function uploadFromDirectory(string $path, ClientConfiguration $clientConfiguration): void
    {
        $videoFilesNames = scandir($path);

        foreach ($videoFilesNames as $videoFileName) {
            if (in_array(pathinfo($videoFileName, PATHINFO_EXTENSION), self::VALID_EXTENSIONS, true)) {
                $videoTitle = pathinfo($videoFileName, PATHINFO_FILENAME);
                $videoFilePath = sprintf('%s%s',$path, $videoFileName);

                try {
                    $this->videoService->upload(
                        YouTubeVideoFactory::build(
                            $videoFilePath,
                            $videoTitle,
                            [
                                'reddit',
                                'redditvideos',
                                'redditthreads',
                                'redditstories',
                                'redditthreading',
                                'redditreading',
                            ]
                        ),
                        $clientConfiguration
                    );
                } catch (\Exception $exception) {
                    throw new VideoException('Cannot upload video', $exception);
                }
            }
        }
    }
}
