<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\DTO\Recipe;

final class StoreRecipeDTO
{
    /**
     * @param IngredientDTO[] $ingredients
     * @param RecipeResultDTO[] $results
     */
    public function __construct(
        private readonly string $name,
        private readonly array $ingredients,
        private readonly array $results,
        private readonly array $itemsInRecipeIds
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getIngredients(): array
    {
        return $this->ingredients;
    }

    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * @return int[] array
     */
    public function getItemsInRecipeIds(): array
    {
        return $this->itemsInRecipeIds;
    }
}
