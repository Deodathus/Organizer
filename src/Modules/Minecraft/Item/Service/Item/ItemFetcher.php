<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Item;

use App\Modules\Minecraft\Item\Entity\Item;
use App\Modules\Minecraft\Item\Exception\CannotFetchItemsException;
use App\Modules\Minecraft\Item\Exception\ItemDoesNotExist;
use App\Modules\Minecraft\Item\Repository\ItemRepository;
use App\Modules\Minecraft\Item\Search\FilterBus;
use App\Modules\Minecraft\Item\Search\PaginatedResult;
use Psr\Log\LoggerInterface;

final class ItemFetcher
{
    public function __construct(
        private readonly ItemRepository $itemRepository,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * @throws ItemDoesNotExist
     */
    public function fetch(int $id): Item
    {
        if ($item = $this->itemRepository->find($id)) {
            return $item;
        }

        throw new ItemDoesNotExist(sprintf('Item ID: %d', $id));
    }

    public function fetchAllPaginated(FilterBus $filterBus): PaginatedResult
    {
        try {
            $items = $this->itemRepository->fetchAllPaginated($filterBus);
        } catch (CannotFetchItemsException $exception) {
            $this->logger->warning($exception->getMessage());

            throw new CannotFetchItemsException('Cannot fetch items!');
        }

        return $items;
    }
}
