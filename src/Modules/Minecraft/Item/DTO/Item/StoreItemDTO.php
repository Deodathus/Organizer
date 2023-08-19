<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\DTO\Item;

use App\Modules\Minecraft\Item\Enum\ItemTypes;

final class StoreItemDTO
{
    public function __construct(
        private readonly ItemTypes $itemType,
        private readonly int $key,
        private readonly ?int $subKey,
        private readonly string $name,
        private readonly ?string $itemTag,
        private readonly ?string $fluidName
    ) {
    }

    public function getItemType(): ItemTypes
    {
        return $this->itemType;
    }

    public function getKey(): int
    {
        return $this->key;
    }

    public function getSubKey(): ?int
    {
        return $this->subKey;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getItemTag(): ?string
    {
        return $this->itemTag;
    }

    public function getFluidName(): ?string
    {
        return $this->fluidName;
    }
}
