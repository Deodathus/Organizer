<?php

declare(strict_types=0);

namespace App\Modules\Minecraft\Item\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'minecraft:database:clear',
    description: 'Truncate all minecraft tables'
)]
final class ClearDatabaseCommand extends Command
{
    public function __construct(private readonly Connection $connection)
    {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->connection->beginTransaction();

        $ingredientsDeleted = $this->connection->createQueryBuilder()
            ->delete('ingredient')
            ->where('id > 0')
            ->executeStatement();

        $output->writeln(sprintf('<info>%d ingredients deleted</info>', $ingredientsDeleted));

        $recipesIngredientsDeleted = $this->connection
            ->createQueryBuilder()
            ->delete('recipes_ingredients')
            ->where('recipe_id > 0')
            ->executeStatement();

        $output->writeln(sprintf('<info>%d recipes ingredients deleted</info>', $recipesIngredientsDeleted));

        $recipeResultsDeleted = $this->connection
            ->createQueryBuilder()
            ->delete('recipe_result')
            ->where('id > 0')
            ->executeStatement();

        $output->writeln(sprintf('<info>%d recipe results deleted</info>', $recipeResultsDeleted));

        $recipesDeleted = $this->connection
            ->createQueryBuilder()
            ->delete('recipe')
            ->where('id > 0')
            ->executeStatement();
        $output->writeln(sprintf('<info>%d recipes deleted</info>', $recipesDeleted));

        $itemsDeleted = $this->connection
            ->createQueryBuilder()
            ->delete('items')
            ->where('id > 0')
            ->executeStatement();
        $output->writeln(sprintf('<info>%d items deleted</info>', $itemsDeleted));

        $this->connection->commit();

        return 0;
    }
}
