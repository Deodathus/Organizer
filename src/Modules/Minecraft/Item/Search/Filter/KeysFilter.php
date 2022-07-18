<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Search\Filter;

final class KeysFilter
{
    public function __construct(
        private readonly int $mainKey,
        private readonly ?int $subKey
    ) {}

    public function getMainKey(): int
    {
        return $this->mainKey;
    }

    public function getSubKey(): ?int
    {
        return $this->subKey;
    }
}
