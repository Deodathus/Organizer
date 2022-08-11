<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\CraftCalculator\DTO;

final class IngredientDTO
{
    public function __construct(
<<<<<<< HEAD
        private readonly int $id,
        private readonly int $amount,
        private readonly int $itemId,
        private readonly string $itemName
    ) {
    }
=======
        private readonly array $ingredientItems
    ) {}
>>>>>>> Calculator and tree calculator fitted to multi items ingredients.

    /**
     * @return IngredientItemDTO[]
     */
    public function getItems(): array
    {
        return $this->ingredientItems;
    }

<<<<<<< HEAD
    #[ArrayShape(['id' => 'int', 'itemId' => 'int', 'amount' => 'int', 'itemName' => 'string'])]
    #[Pure]
=======
>>>>>>> Calculator and tree calculator fitted to multi items ingredients.
    public function toArray(): array
    {
        return array_map(static fn($ingredientItem): array => $ingredientItem->toArray(), $this->ingredientItems);
    }
}
