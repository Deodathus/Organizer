<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Command;

use App\Modules\Minecraft\Item\Service\Recipe\Importer\GTRecipeImporter;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'minecraft:gt-recipes:import',
    description: 'imports gregtech recipes from json with given path'
)]
final class ImportGTRecipesCommand extends Command
{
    private const FILE_PATH_ARGUMENT = 'filePath';

    public function __construct(
        private readonly GTRecipeImporter $importer,
        private readonly LoggerInterface $logger,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                name: self::FILE_PATH_ARGUMENT,
                mode: InputArgument::REQUIRED,
                description: 'JSON with recipes path'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->importer->import(
                filePath: $input->getArgument(self::FILE_PATH_ARGUMENT)
            );
        } catch (\Exception $exception) {
            $this->logger->critical($exception->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
