<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Item\Factory;

use App\Modules\Minecraft\Item\DTO\Item\StoreItemDTO;
use App\Modules\Minecraft\Item\Entity\Item;
use JetBrains\PhpStorm\Pure;

final class ItemFactory
{
    #[Pure]
    public static function build(StoreItemDTO $itemDTO): Item
    {
        return new Item(
            name: $itemDTO->getName(),
            key: $itemDTO->getKey(),
            subKey: $itemDTO->getSubKey()
        );
    }
}
