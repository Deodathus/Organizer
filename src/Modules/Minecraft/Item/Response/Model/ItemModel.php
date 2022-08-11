<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Response\Model;

use JetBrains\PhpStorm\ArrayShape;

final class ItemModel
{
    public function __construct(
        private readonly int $id,
        private readonly string $type,
        private readonly int $key,
        private readonly ?int $subKey,
        private readonly string $name,
        private readonly ?string $itemTag,
        private readonly ?string $fluidName
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

    #[ArrayShape([
        'id' => "int",
        'type' => "string",
        'key' => "int",
        'subKey' => "int|null",
        'name' => "string",
        'itemTag' => "string|null",
        'fluidName' => "string|null"
    ])]
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'id' => $this->id,
            'key' => $this->key,
            'subKey' => $this->subKey,
            'name' => $this->name,
            'itemTag' => $this->itemTag,
            'fluidName' => $this->fluidName,
        ];
    }
}
