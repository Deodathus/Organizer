<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Recipe\Importer;

use App\Modules\Minecraft\Item\DTO\Recipe\Import\RecipeDTO;

interface RecipeImportProcessorInterface
{
    public function process(RecipeDTO $recipeDTO): void;
}
