<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Item\Importer;

use App\Modules\Minecraft\Item\DTO\Item\StoreItemDTO;

interface ItemImportProcessorInterface
{
    public function process(StoreItemDTO $itemDTO): void;
}
