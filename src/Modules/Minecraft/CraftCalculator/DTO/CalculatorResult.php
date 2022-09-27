<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\CraftCalculator\DTO;

use Doctrine\Common\Collections\ArrayCollection;

final readonly class CalculatorResult
{
    /**
     * @param IngredientDTO[] $ingredients
     * @param ResultDTO[] $results
     */
    public function __construct(
        private int   $calculableId,
        private array $ingredients,
        private array $results
    ) {
    }

    public function getCalculableId(): int
    {
        return $this->calculableId;
    }

    /**
     * @return ArrayCollection<IngredientDTO>
     */
    public function getIngredients(): ArrayCollection
    {
        return new ArrayCollection($this->ingredients);
    }

    /**
     * @return ArrayCollection<ResultDTO>
     */
    public function getResults(): ArrayCollection
    {
        return new ArrayCollection($this->results);
    }

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
