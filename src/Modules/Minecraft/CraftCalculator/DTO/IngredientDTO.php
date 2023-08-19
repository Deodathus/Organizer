<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\CraftCalculator\DTO;

final readonly class IngredientDTO
{
    /**
     * @param IngredientItemDTO[] $ingredientItems
     */
    public function __construct(
        private array $ingredientItems
    ) {
    }

    /**
     * @return IngredientItemDTO[]
     */
    public function getItems(): array
    {
        return $this->ingredientItems;
    }

    /**
     * @return array<array{id: int, itemId: int, amount: int, itemName: string}>
     */
    public function toArray(): array
    {
        return array_map(static fn ($ingredientItem): array => $ingredientItem->toArray(), $this->ingredientItems);
    }
}
