<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Item\Factory;

use App\Modules\Minecraft\Item\DTO\Item\StoreItemDTO;
use App\Modules\Minecraft\Item\Entity\Item;

interface ItemFactoryInterface
{
    public function build(StoreItemDTO $itemDTO): Item;
}
