<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\DTO\Recipe\CalculatedTree;

final class TreeIngredientDTO
{
    public function __construct(
<<<<<<< HEAD
        private readonly float $amount,
        private readonly int $itemId,
        private readonly string $itemName,
        private readonly array $asResult
    ) {
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getItemId(): int
    {
        return $this->itemId;
    }

    public function getItemName(): string
    {
        return $this->itemName;
    }

    /**
     * @return TreeIngredientDTO[]
     */
    public function getAsResult(): array
    {
        return $this->asResult;
    }

    #[ArrayShape(['itemId' => 'int', 'itemName' => 'string', 'amount' => 'float', 'asResult' => 'array'])]
    #[Pure]
=======
        private readonly array $treeIngredientItems
    ) {}

>>>>>>> Calculator and tree calculator fitted to multi items ingredients.
    public function toArray(): array
    {
        return array_map(static fn($treeIngredientItem): array => $treeIngredientItem->toArray(), $this->treeIngredientItems);
    }
}
