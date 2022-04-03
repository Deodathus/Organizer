<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\DTO\Recipe;

final class RecipeResultDTO
{
    public function __construct(
        private int $amount,
        private int $itemId
    ) {}

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getItemId(): int
    {
        return $this->itemId;
    }
}
