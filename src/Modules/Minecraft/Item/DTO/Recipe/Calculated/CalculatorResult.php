<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\DTO\Recipe\Calculated;

use Doctrine\Common\Collections\ArrayCollection;
use JetBrains\PhpStorm\ArrayShape;

final class CalculatorResult
{
    /**
     * @param IngredientDTO[] $ingredients
     * @param RecipeResultDTO[] $results
     */
    public function __construct(
        private readonly int $calculableId,
        private readonly array $ingredients,
        private readonly array $results
    ) {
    }

    /**
     * @return ArrayCollection<IngredientDTO>
     */
    public function getIngredients(): ArrayCollection
    {
        return new ArrayCollection($this->ingredients);
    }

    /**
     * @return ArrayCollection<RecipeResultDTO>
     */
    public function getResults(): ArrayCollection
    {
        return new ArrayCollection($this->results);
    }

    #[ArrayShape(['calculableId' => 'int', 'ingredients' => 'array', 'results' => 'array'])]
    public function toArray(): array
    {
        $ingredients = [];
        $results = [];

        foreach ($this->ingredients as $ingredient) {
            $ingredients[] = $ingredient->toArray();
        }

        foreach ($this->results as $result) {
            $results[] = $result->toArray();
        }

        return [
            'calculableId' => $this->calculableId,
            'ingredients' => $ingredients,
            'results' => $results,
        ];
    }
}
