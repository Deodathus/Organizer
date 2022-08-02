<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Messenger\Handler;

use App\Modules\Minecraft\Item\Messenger\Message\ImportRecipe;
use App\Modules\Minecraft\Item\Service\Recipe\Importer\RecipeImportProcessorInterface;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class RecipeImportHandler
{
    public function __construct(
        private readonly RecipeImportProcessorInterface $importProcessor
    ) {}

    #[NoReturn]
    public function __invoke(ImportRecipe $importRecipe): void
    {
        $this->importProcessor->process(
            recipeDTO: $importRecipe->getRecipe()
        );
    }
}
