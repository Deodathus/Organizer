<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\CraftCalculator\DTO;

final readonly class TreeIngredientDTO
{
    /**
     * @param TreeIngredientItemDTO[] $treeIngredientItems
     */
    public function __construct(
        private readonly array $treeIngredientItems
    ) {}

    /**
     * @return TreeIngredientItemDTO[]
     */
    public function getItems(): array
    {
        return $this->treeIngredientItems;
    }

    public function toArray(): array
    {
        return array_map(static fn ($treeIngredientItem): array => $treeIngredientItem->toArray(), $this->treeIngredientItems);
    }
}