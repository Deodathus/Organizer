<?php
declare(strict_types=1);

namespace App\Modules\Authentication\Domain\Entity;

use App\Modules\Authentication\Domain\ValueObject\RefreshToken;
use App\Modules\Authentication\Domain\ValueObject\UserExternalId;
use App\Modules\Authentication\Domain\ValueObject\Token;
use App\Modules\Authentication\Domain\ValueObject\UserId;
use App\Modules\Authentication\Domain\ValueObject\UserName;

final class User
{
    private function __construct(
        private readonly UserId $userId,
        private readonly UserExternalId $externalUserId,
        private readonly Token $token,
        private readonly RefreshToken $refreshToken,
        private UserName $name
    ) {}

    public static function register(
        UserExternalId $externalUserId,
        Token $token,
        RefreshToken $refreshToken,
        UserName $name
    ): self {
        return new User(
            UserId::generate(),
            $externalUserId,
            $token,
            $refreshToken,
            $name
        );
    }

    public static function reproduce(
        UserId $id,
        UserExternalId $externalUserId,
        Token $token,
        RefreshToken $refreshToken,
        UserName $name
    ): self {
        return new self(
            $id,
            $externalUserId,
            $token,
            $refreshToken,
            $name
        );
    }

    public function changeName(UserName $name): void
    {
        $this->name = $name;
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public function getExternalUserId(): UserExternalId
    {
        return $this->externalUserId;
    }

    public function getToken(): Token
    {
        return $this->token;
    }

    public function getRefreshToken(): RefreshToken
    {
        return $this->refreshToken;
    }

    public function getName(): UserName
    {
        return $this->name;
    }
}
