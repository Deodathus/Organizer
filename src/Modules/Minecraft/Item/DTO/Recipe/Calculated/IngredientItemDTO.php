<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\DTO\Recipe\Calculated;

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

final class IngredientItemDTO
{
    public function __construct(
        private readonly int $id,
        private readonly int $amount,
        private readonly int $itemId,
        private readonly string $itemName
    ) {}

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

    #[ArrayShape(['id' => "int", 'itemId' => "int", 'amount' => "int", 'itemName' => "string"])]
    #[Pure]
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'itemId' => $this->getItemId(),
            'amount' => $this->getAmount(),
            'itemName' => $this->getItemName(),
        ];
    }
}
