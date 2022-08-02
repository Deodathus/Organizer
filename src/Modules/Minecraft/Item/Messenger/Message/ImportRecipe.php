<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Messenger\Message;

use App\Modules\Minecraft\Item\DTO\Recipe\Import\RecipeDTO;

final class ImportRecipe
{
    public function __construct(
        private readonly RecipeDTO $recipe
    ) {}

    public function getRecipe(): RecipeDTO
    {
        return $this->recipe;
    }
}
