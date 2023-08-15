<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Transformer;

use App\Modules\Minecraft\Item\Entity\Item;
use App\Modules\Minecraft\Item\Response\Model\ItemRecipesModel;

interface ItemToItemRecipesModelTransformerInterface
{
    public function transform(Item $item): ItemRecipesModel;
}
