<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Item\Importer;

use App\Modules\Minecraft\Item\DTO\Item\StoreItemDTO;
use App\Modules\Minecraft\Item\Service\Item\ItemPersister;

final class ItemImportProcessor implements ItemImportProcessorInterface
{
    public function __construct(
        private readonly ItemPersister $itemPersister
    ) {
    }

    public function process(StoreItemDTO $itemDTO): void
    {
        $this->itemPersister->store($itemDTO);
    }
}
