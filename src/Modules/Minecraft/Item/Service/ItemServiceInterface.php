<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service;

use App\Modules\Minecraft\Item\DTO\SaveItemDTO;
use App\Modules\Minecraft\Item\Entity\Item;

interface ItemServiceInterface
{
    public function fetch(int $id): Item;

    public function store(SaveItemDTO $itemDTO): int;
}
