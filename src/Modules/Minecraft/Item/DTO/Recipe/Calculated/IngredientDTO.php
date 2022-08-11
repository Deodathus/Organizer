<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\DTO\Recipe\Calculated;

final class IngredientDTO
{
    /**
     * @param IngredientItemDTO[]
     */
    public function __construct(
<<<<<<< HEAD
        private readonly int $id,
        private readonly int $amount,
        private readonly int $itemId,
        private readonly string $itemName
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAmount(): int
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

    #[ArrayShape(['id' => 'int', 'itemId' => 'int', 'amount' => 'int', 'itemName' => 'string'])]
    #[Pure]
=======
        private readonly array $ingredientItems
    ) {}

>>>>>>> Calculator and tree calculator fitted to multi items ingredients.
    public function toArray(): array
    {
        return array_map(static fn($ingredientItem): array => $ingredientItem->toArray(), $this->ingredientItems);
    }
}
