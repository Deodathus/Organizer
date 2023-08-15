<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Item;

use App\Modules\Minecraft\Item\Repository\ItemRepository;

final class ItemRemover
{
    public function __construct(
        private readonly ItemRepository $itemRepository
    ) {
    }

    public function deleteById(int $id): void
    {
        $this->itemRepository->deleteById($id);
    }
}
