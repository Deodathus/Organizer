<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\CraftCalculator\DTO;

use JetBrains\PhpStorm\ArrayShape;

final class TreeRecipeResultDTO
{
    public function __construct (
        private readonly int $amount,
        private readonly int $itemId,
        private readonly string $itemName
    ) {}

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

    #[ArrayShape(['amount' => "int", 'itemId' => "int", 'itemName' => "string"])]
    public function toArray(): array
    {
        return [
            'amount' => $this->getAmount(),
            'itemId' => $this->getItemId(),
            'itemName' => $this->getItemName(),
        ];
    }
}
