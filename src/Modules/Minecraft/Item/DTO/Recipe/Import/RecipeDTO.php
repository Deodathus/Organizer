<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\DTO\Recipe\Import;

final class RecipeDTO
{
    /**
     * @param IngredientDTO[] $ingredients
     * @param RecipeResultDTO[] $recipeResults
     */
    public function __construct(
        private readonly array $ingredients,
        private readonly array $recipeResults
    ) {}

    /**
     * @return IngredientDTO[]
     */
    public function getIngredients(): array
    {
        return $this->ingredients;
    }

    /**
     * @return RecipeResultDTO[]
     */
    public function getRecipeResults(): array
    {
        return $this->recipeResults;
    }
}
