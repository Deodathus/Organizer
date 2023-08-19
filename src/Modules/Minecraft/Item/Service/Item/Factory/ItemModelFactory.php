<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Item\Factory;

use App\Modules\Minecraft\Item\Entity\Fluid;
use App\Modules\Minecraft\Item\Entity\FluidCell;
use App\Modules\Minecraft\Item\Entity\Item;
use App\Modules\Minecraft\Item\Response\Model\ItemModel;

final class ItemModelFactory
{
    public function build(Item $item): ItemModel
    {
        $fluidName = match (get_class($item)) {
            default => null,
            FluidCell::class => $item->getFluidName(),
            Fluid::class => $item->getFluidName(),
        };

        return new ItemModel(
            $item->getId(),
            $item->getDiscriminator(),
            $item->getKey(),
            $item->getSubKey(),
            $item->getName(),
            $item->getItemTag(),
            $fluidName
        );
    }
}
