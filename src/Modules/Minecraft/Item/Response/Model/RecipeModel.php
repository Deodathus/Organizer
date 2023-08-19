<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Response\Model;

final readonly class RecipeModel
{
    /**
     * @param IngredientModel[] $ingredients
     * @param RecipeResultModel[] $results
     */
    public function __construct(
        public int $id,
        public string $name,
        public array $ingredients,
        public array $results
    ) {
    }

    public function toArray(): array
    {
        $recipe = [
            'id' => $this->id,
            'name' => $this->name,
        ];

        foreach ($this->ingredients as $ingredient) {
            $recipe['ingredients'][] = $ingredient->toArray();
        }

        foreach ($this->results as $result) {
            $recipe['results'][] = $result->toArray();
        }

        return $recipe;
    }
}
