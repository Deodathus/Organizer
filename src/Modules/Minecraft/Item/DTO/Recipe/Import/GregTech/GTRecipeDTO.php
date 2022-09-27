<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\DTO\Recipe\Import\GregTech;

use App\Modules\Minecraft\Item\DTO\Recipe\Import\IngredientDTO;
use App\Modules\Minecraft\Item\DTO\Recipe\Import\RecipeResultDTO;

final class GTRecipeDTO
{
    /**
     * @param IngredientDTO[] $ingredients
     * @param IngredientDTO[] $fluidIngredients
     * @param RecipeResultDTO[] $recipeResults
     * @param RecipeResultDTO[] $fluidRecipeResults
     * @param int[] $recipeChances
     */
    public function __construct(
        public readonly array $ingredients,
        public readonly array $fluidIngredients,
        public readonly array $recipeResults,
        public readonly array $fluidRecipeResults,
        public readonly array $recipeChances,
        public readonly bool $isElectrical,
        public readonly int $euPerTick,
        public readonly int $duration,
        public readonly int $generatesEnergy
    ) {}
}
