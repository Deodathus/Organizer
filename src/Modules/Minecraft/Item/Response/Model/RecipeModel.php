<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Response\Model;

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

final class RecipeModel
{
    /**
     * @param IngredientModel[] $ingredients
     * @param RecipeResultModel[] $results
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly array $ingredients,
        public readonly array $results
    ) {}

    #[ArrayShape(['id' => "int", 'name' => "string", 'results' => "array", 'ingredients' => "array"])]
    #[Pure]
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
