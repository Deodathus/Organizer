<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Application\DTO;

final class ExternalUserDTO
{
    public function __construct(
        public readonly string $externalId,
        public readonly string $token,
        public readonly string $refreshToken
    ) {
    }
}
