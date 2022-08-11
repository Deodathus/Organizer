<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Item\Factory;

use App\Modules\Minecraft\Item\DTO\Item\StoreItemDTO;
use App\Modules\Minecraft\Item\Entity\Fluid;
use App\Modules\Minecraft\Item\Entity\FluidCell;
use App\Modules\Minecraft\Item\Entity\Item;
use App\Modules\Minecraft\Item\Enum\ItemTypes;
use RuntimeException;

final class ItemFactory implements ItemFactoryInterface
{
    public function build(StoreItemDTO $itemDTO): Item
    {
        return match ($itemDTO->getItemType()) {
            ItemTypes::ITEM => new Item(
                name: $itemDTO->getName(),
                key: $itemDTO->getKey(),
                subKey: $itemDTO->getSubKey(),
                itemTag: $itemDTO->getItemTag()
            ),
            ItemTypes::FLUID_CELL => new FluidCell(
                name: $itemDTO->getName(),
                key: $itemDTO->getKey(),
                subKey: $itemDTO->getSubKey(),
                itemTag: $itemDTO->getItemTag(),
                fluidName: $itemDTO->getFluidName()
            ),
            ItemTypes::FLUID => new Fluid(
                name: $itemDTO->getName(),
                key: $itemDTO->getKey(),
                subKey: $itemDTO->getSubKey(),
                itemTag: $itemDTO->getItemTag(),
                fluidName: $itemDTO->getFluidName()
            ),
            default => throw new RuntimeException(
                sprintf('%s is not supported', $itemDTO->getItemType()->value)
            ),
        };
    }
}
