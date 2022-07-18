<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Item;

use App\Modules\Minecraft\Item\DTO\Item\StoreItemDTO;
use App\Modules\Minecraft\Item\Entity\Item;
use App\Modules\Minecraft\Item\Repository\ItemRepository;

final class ItemPersister
{
    public function __construct(
        private readonly ItemRepository $itemRepository
    ) {
    }

    public function store(StoreItemDTO $itemDTO): Item
    {
        $item = new Item(name: $itemDTO->getName(), key: $itemDTO->getKey(), subKey: $itemDTO->getSubKey());

        $this->itemRepository->store($item);
        $this->itemRepository->flush();

        return $item;
    }
}
