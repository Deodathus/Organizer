<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Messenger\Message;

use App\Modules\Minecraft\Item\DTO\Recipe\Import\GregTech\GTRecipeDTO;

final class ImportGTRecipe
{
    public function __construct(
        private readonly GTRecipeDTO $recipe
    ) {
    }

    public function getRecipe(): GTRecipeDTO
    {
        return $this->recipe;
    }
}
