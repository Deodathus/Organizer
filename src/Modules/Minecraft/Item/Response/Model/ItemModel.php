<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Response\Model;

use JetBrains\PhpStorm\ArrayShape;

final readonly class ItemModel
{
    public function __construct(
        private int     $id,
        private string  $type,
        private int     $key,
        private ?int    $subKey,
        private string  $name,
        private ?string $itemTag,
        private ?string $fluidName
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
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

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'key' => $this->key,
            'subKey' => $this->subKey,
            'name' => $this->name,
            'itemTag' => $this->itemTag,
            'fluidName' => $this->fluidName,
        ];
    }
}
