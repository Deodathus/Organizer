<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Response\Model;

use JetBrains\PhpStorm\ArrayShape;

class ItemModel
{
    public function __construct(
        private int $id,
        private int $key,
        private ?int $subKey,
        private string $name
    ) {}

    public function getId(): int
    {
        return $this->id;
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

    #[ArrayShape(['id' => "int", 'subId' => "int|null", 'name' => "string"])]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'key' => $this->key,
            'subId' => $this->subKey,
            'name' => $this->name,
        ];
    }
}
