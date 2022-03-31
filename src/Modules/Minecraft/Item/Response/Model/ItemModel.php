<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Response\Model;

use JetBrains\PhpStorm\ArrayShape;

class ItemModel
{
    public function __construct(private int $id, private ?int $subId, private string $name)
    {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getSubId(): ?int
    {
        return $this->subId;
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
            'subId' => $this->subId,
            'name' => $this->name,
        ];
    }
}
