<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Item;

use App\Modules\Minecraft\Item\DTO\Item\StoreItemDTO;
use App\Modules\Minecraft\Item\Entity\Item;

interface ItemServiceInterface
{
    public function fetch(int $id): Item;

    /**
     * @return Item[]
     */
    public function fetchAll(): array;

    public function store(StoreItemDTO $itemDTO): int;

    public function deleteById(int $id);
}
