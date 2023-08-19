<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\DTO\Recipe\CalculatedTree;

final class TreeIngredientDTO
{
    public function __construct(
        private readonly array $treeIngredientItems
    ) {}

    public function toArray(): array
    {
        return array_map(static fn($treeIngredientItem): array => $treeIngredientItem->toArray(), $this->treeIngredientItems);
    }
}
