<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Response\Model;

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

final class RecipeResultModel
{
    public function __construct(
        private int $id,
        private int $amount,
        private int $itemId,
        private string $itemName
    ) {}

    #[ArrayShape(['id' => "int", 'amount' => "int", 'itemId' => "int", 'itemName' => "string"])]
    #[Pure]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'itemId' => $this->itemId,
            'itemName' => $this->itemName
        ];
    }
}
