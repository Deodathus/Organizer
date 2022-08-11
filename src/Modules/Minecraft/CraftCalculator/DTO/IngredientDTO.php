<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\CraftCalculator\DTO;

final class IngredientDTO
{
    public function __construct(
        private readonly array $ingredientItems
    ) {}

    /**
     * @return IngredientItemDTO[]
     */
    public function getItems(): array
    {
        return $this->ingredientItems;
    }

    public function toArray(): array
    {
        return array_map(static fn($ingredientItem): array => $ingredientItem->toArray(), $this->ingredientItems);
    }
}
