<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\CraftCalculator\DTO;

use Doctrine\Common\Collections\ArrayCollection;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

final class CalculatorResult
{
    /**
     * @param IngredientDTO[] $ingredients
     */
    public function __construct(
        private int $calculableId,
        private array $ingredients
    ) {}

    /**
     * @return ArrayCollection<IngredientDTO>
     */
    #[Pure]
    public function getIngredients(): ArrayCollection
    {
        return new ArrayCollection($this->ingredients);
    }

    #[ArrayShape(['calculableId' => "int", 'ingredients' => "array"])]
    #[Pure]
    public function toArray(): array
    {
        $ingredients = [];

        foreach ($this->ingredients as $ingredient) {
            $ingredients[] = $ingredient->toArray();
        }

        return [
            'calculableId' => $this->calculableId,
            'ingredients' => $ingredients,
        ];
    }
}