<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Item\Importer;

use App\Modules\Minecraft\Item\DTO\Item\StoreItemDTO;
use App\Modules\Minecraft\Item\Enum\ItemTypes;
use App\Modules\Minecraft\Item\Exception\ItemImporterException;
use App\Modules\Minecraft\Item\Messenger\Message\ImportItem;
use JsonMachine\Exception\InvalidArgumentException;
use JsonMachine\Items;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class ItemImporter
{
    public function __construct(
        private MessageBusInterface $messageBus
    ) {
    }

    public function import(string $filePath): void
    {
        try {
            $items = Items::fromFile($filePath);
        } catch (InvalidArgumentException $e) {
            throw new ItemImporterException($e);
        }

        foreach ($items as $item) {
            $itemType = ItemTypes::ITEM;
            if (property_exists($item, 'type')) {
                $itemType = match ($item->type) {
                    default => ItemTypes::ITEM,
                    ItemTypes::FLUID->value => ItemTypes::FLUID,
                    ItemTypes::FLUID_CELL->value => ItemTypes::FLUID_CELL,
                };
            }

            $fluidName = '';
            if (property_exists($item, 'fluidName')) {
                $fluidName = $item->fluidName;
            }

            $this->messageBus->dispatch(
                new ImportItem(
                    new StoreItemDTO(
                        $itemType,
                        $item->key,
                        $item->subKey,
                        $item->name,
                        $item->fullName ?? null,
                        fluidName: $fluidName
                    )
                )
            );
        }
    }
}
