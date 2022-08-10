<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\DTO\Item;

final class StoreItemDTO
{
    public function __construct(
        private readonly int $key,
        private readonly ?int $subKey,
        private readonly string $name,
        private readonly ?string $itemTag
    ){}

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
}
