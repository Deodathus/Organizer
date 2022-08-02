<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Messenger\Handler;

use App\Modules\Minecraft\Item\Messenger\Message\ImportItem;
use App\Modules\Minecraft\Item\Service\Item\Importer\ItemImportProcessorInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ItemImportHandler
{
    public function __construct(
        private readonly ItemImportProcessorInterface $importProcessor
    ) {}

    public function __invoke(ImportItem $importItem): void
    {
        $this->importProcessor->process($importItem->getItem());
    }
}
