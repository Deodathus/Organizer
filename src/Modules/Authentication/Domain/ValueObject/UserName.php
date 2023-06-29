<?php
declare(strict_types=1);

namespace App\Modules\Authentication\Domain\ValueObject;

final class UserName
{
    public function __construct(
        public readonly string $firstName,
        public readonly string $lastName
    ) {}
}
