<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use Ramsey\Uuid\Uuid;

abstract class Id
{
    private function __construct(private readonly string $id)
    {
    }

    public static function fromString(string $id): static
    {
        return new static($id);
    }

    public static function generate(): static
    {
        return new static(Uuid::uuid4()->toString());
    }

    public function toString(): string
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
