<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Item\Importer;

use App\Modules\Minecraft\Item\DTO\Item\StoreItemDTO;
use App\Modules\Minecraft\Item\Exception\ItemImporterException;
use App\Modules\Minecraft\Item\Repository\ItemRepository;
use App\Modules\Minecraft\Item\Service\Item\Factory\ItemFactory;
use JsonMachine\Exception\InvalidArgumentException;
use JsonMachine\Items;

final class ItemImporter
{
    public function __construct(
        private readonly ItemRepository $itemRepository
    ) {}

    public function import(string $filePath): void
    {
        try {
            $items = Items::fromFile($filePath);
        } catch (InvalidArgumentException $e) {
            throw new ItemImporterException($e);
        }

        foreach ($items as $item) {
            $item = ItemFactory::build(
                new StoreItemDTO(
                    key: $item->key,
                    subKey: $item->subKey,
                    name: $item->name
                )
            );
            $this->itemRepository->store($item);

        }

        $this->itemRepository->flush();
    }
}
