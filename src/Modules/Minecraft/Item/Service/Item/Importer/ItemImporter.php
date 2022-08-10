<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Item\Importer;

use App\Modules\Minecraft\Item\DTO\Item\StoreItemDTO;
use App\Modules\Minecraft\Item\Exception\ItemImporterException;
use App\Modules\Minecraft\Item\Messenger\Message\ImportItem;
use JsonMachine\Exception\InvalidArgumentException;
use JsonMachine\Items;
use Symfony\Component\Messenger\MessageBusInterface;

final class ItemImporter
{
    public function __construct(
        private readonly MessageBusInterface $messageBus
    ) {}

    public function import(string $filePath): void
    {
        try {
            $items = Items::fromFile($filePath);
        } catch (InvalidArgumentException $e) {
            throw new ItemImporterException($e);
        }

        foreach ($items as $item) {
            $this->messageBus->dispatch(
                new ImportItem(
                    new StoreItemDTO(
                        $item->key,
                        $item->subKey,
                        $item->name,
                        $item->fullName ?? null
                    )
                )
            );
        }
    }
}
