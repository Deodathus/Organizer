<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Messenger\Message;

use App\Modules\Minecraft\Item\DTO\Item\StoreItemDTO;

final class ImportItem
{
    public function __construct(
        private readonly StoreItemDTO $item
    ) {
    }

    public function getItem(): StoreItemDTO
    {
        return $this->item;
    }
}
