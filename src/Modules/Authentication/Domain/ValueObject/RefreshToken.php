<?php
declare(strict_types=1);

namespace App\Modules\Authentication\Domain\ValueObject;

final class RefreshToken
{
    public function __construct(
        public readonly string $value
    ) {}
}
