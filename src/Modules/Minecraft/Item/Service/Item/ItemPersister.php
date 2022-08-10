<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Item;

use App\Modules\Minecraft\Item\DTO\Item\StoreItemDTO;
use App\Modules\Minecraft\Item\Entity\Item;
use App\Modules\Minecraft\Item\Enum\ItemTypes;
use App\Modules\Minecraft\Item\Repository\ItemRepository;
use App\Modules\Minecraft\Item\Service\Item\Factory\ItemFactoryInterface;

final class ItemPersister
{
    public function __construct(
        private readonly ItemFactoryInterface $itemFactory,
        private readonly ItemRepository $itemRepository
    ) {
    }

    public function store(StoreItemDTO $itemDTO): Item
    {
        $item = $this->itemFactory->build($itemDTO);

        $this->itemRepository->store($item);
        $this->itemRepository->flush();

        return $item;
    }
}
