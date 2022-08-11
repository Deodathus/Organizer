<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\CraftCalculator\DTO;

final class TreeIngredientDTO
{
    /**
     * @param TreeIngredientItemDTO[]
     */
    public function __construct(
<<<<<<< HEAD
        private readonly float $amount,
        private readonly int $itemId,
        private readonly string $itemName,
        private readonly array $asResult
    ) {
    }
=======
        private readonly array $treeIngredientItems
    ) {}
>>>>>>> Calculator and tree calculator fitted to multi items ingredients.

    public function getItems(): array
    {
        return $this->treeIngredientItems;
    }

<<<<<<< HEAD
    #[ArrayShape(['itemId' => 'int', 'itemName' => 'string', 'amount' => 'float', 'asResult' => 'array'])]
    #[Pure]
=======
>>>>>>> Calculator and tree calculator fitted to multi items ingredients.
    public function toArray(): array
    {
        return array_map(static fn ($treeIngredientItem): array => $treeIngredientItem->toArray(), $this->treeIngredientItems);
    }
}
