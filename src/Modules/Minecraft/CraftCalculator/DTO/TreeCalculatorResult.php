<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\CraftCalculator\DTO;

use Doctrine\Common\Collections\ArrayCollection;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

final class TreeCalculatorResult
{
    /**
     * @param TreeIngredientDTO[] $ingredients
     * @param TreeRecipeResultDTO[] $results
     */
    public function __construct(
        private readonly int $calculableId,
        private readonly array $results,
        private readonly array $ingredients
    ) {}

    public function getRecipeResults(): array
    {
        return $this->results;
    }

    /**
     * @return ArrayCollection<TreeIngredientDTO>
     */
    #[Pure]
    public function getIngredients(): ArrayCollection
    {
        return new ArrayCollection($this->ingredients);
    }

    #[ArrayShape(['calculableId' => "int", 'results' => "array", 'ingredients' => "array"])]
    public function toArray(): array
    {
        $results = [];
        $ingredients = [];

        foreach ($this->getRecipeResults() as $recipeResult) {
            $results[] = $recipeResult->toArray();
        }

        foreach ($this->ingredients as $ingredient) {
            $ingredients[] = $ingredient->toArray();
        }

        return [
            'calculableId' => $this->calculableId,
            'results' => $results,
            'ingredients' => $ingredients,
        ];
    }
}
