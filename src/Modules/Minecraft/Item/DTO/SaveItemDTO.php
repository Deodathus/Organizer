<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\DTO;

use Symfony\Component\Validator\Constraints\Type;

final class SaveItemDTO
{
    public function __construct(
        private int $key,
        private ?int $subKey,
        private string $name
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
}
