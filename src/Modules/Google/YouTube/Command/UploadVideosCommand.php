<?php
declare(strict_types=1);

namespace App\Modules\Google\YouTube\Command;

use App\Modules\Google\YouTube\Exception\ClientDoesNotExist;
use App\Modules\Google\YouTube\Exception\VideoException;
use App\Modules\Google\YouTube\Service\ClientConfigurationFetcher;
use App\Modules\Google\YouTube\Service\VideoUploader;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'google:youtube:video:upload',
    description: 'Will upload video to youtube',
    hidden: false
)]
final class UploadVideosCommand extends Command
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly VideoUploader $videoService,
        private readonly ClientConfigurationFetcher $clientConfigurationFetcher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('path', InputArgument::REQUIRED)
            ->addArgument('client_sid', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->videoService->uploadFromDirectory(
                $input->getArgument('path'),
                $this->clientConfigurationFetcher->fetchBySid($input->getArgument('client_sid'))
            );
        } catch (VideoException|ClientDoesNotExist $exception) {
            $this->logger->critical($exception->getMessage());

            return 0;
        }

        return 1;
    }
}
