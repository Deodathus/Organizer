<?php
declare(strict_types=1);

namespace App\Modules\Google\YouTube\DTO\ClientConfiguration;

final class ClientAccessTokenDTO
{
    public function __construct(
        private readonly string $accessToken,
        private readonly int $expiresIn,
        private readonly string $refreshToken,
        private readonly string $scope,
        private readonly string $tokenType,
        private readonly int $created
    ) {}

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    public function getCreated(): int
    {
        return $this->created;
    }
}
