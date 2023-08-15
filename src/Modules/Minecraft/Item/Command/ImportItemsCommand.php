<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Command;

use App\Modules\Minecraft\Item\Service\Item\Importer\ItemImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'item:import',
    description: 'imports items from json with given path'
)]
final class ImportItemsCommand extends Command
{
    private const FILE_PATH_ARGUMENT = 'filePath';

    public function __construct(
        private readonly ItemImporter $importer,
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
                description: 'JSON with items path'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->importer->import(
            $input->getArgument(self::FILE_PATH_ARGUMENT)
        );

        return Command::SUCCESS;
    }
}
