<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\DTO\Recipe\Import;

final class IngredientDTO
{
    /**
     * @param ItemDTO[] $items
     */
    public function __construct(
        private readonly array $items
    ) {}

    /**
     * @return ItemDTO[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
