<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Item;

use App\Modules\Minecraft\Item\DTO\Item\StoreItemDTO;
use App\Modules\Minecraft\Item\Entity\Item;
use App\Modules\Minecraft\Item\Exception\ItemDoesNotExist;
use App\Modules\Minecraft\Item\Repository\ItemRepository;
use Doctrine\ORM\EntityManagerInterface;

class ItemService implements ItemServiceInterface
{
    public function __construct(
        private ItemRepository $itemRepository,
        private EntityManagerInterface $entityManager
    ){}

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

    public function fetchAll(): array
    {
        return $this->itemRepository->findAll();
    }

    public function store(StoreItemDTO $itemDTO): int
    {
        $item = new Item(name: $itemDTO->getName(), key: $itemDTO->getKey(), subKey: $itemDTO->getSubKey());

        $this->entityManager->persist($item);
        $this->entityManager->flush();

        return $item->getId();
    }

    public function deleteById(int $id): void
    {
        $this->itemRepository->deleteById($id);
    }
}