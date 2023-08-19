<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\DTO\Recipe\Calculated;

final class IngredientDTO
{
    /**
     * @param IngredientItemDTO[]
     */
    public function __construct(
        private readonly array $ingredientItems
    ) {}

    public function toArray(): array
    {
        return array_map(static fn($ingredientItem): array => $ingredientItem->toArray(), $this->ingredientItems);
    }
}
