<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\CraftCalculator\DTO;

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

final class IngredientDTO
{
    public function __construct(private readonly int $amount, private readonly int $itemId) {}

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getItemId(): int
    {
        return $this->itemId;
    }

    #[ArrayShape(['itemId' => "int", 'amount' => "int"])]
    #[Pure]
    public function toArray(): array
    {
        return [
            'itemId' => $this->getItemId(),
            'amount' => $this->getAmount(),
        ];
    }
}
