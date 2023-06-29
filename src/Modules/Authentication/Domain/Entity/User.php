<?php
declare(strict_types=1);

namespace App\Modules\Authentication\Domain\Entity;

use App\Modules\Authentication\Domain\ValueObject\ExternalUserId;
use App\Modules\Authentication\Domain\ValueObject\Token;
use App\Modules\Authentication\Domain\ValueObject\UserId;
use App\Modules\Authentication\Domain\ValueObject\UserName;

final class User
{
    private function __construct(
        private readonly UserId $userId,
        private readonly ExternalUserId $externalUserId,
        private readonly Token $token,
        private UserName $name
    ) {}

    public static function register(ExternalUserId $externalUserId, Token $token, UserName $name): self
    {
        return new User(
            UserId::generate(),
            $externalUserId,
            $token,
            $name
        );
    }

    public static function reproduce(UserId $id, ExternalUserId $externalUserId, Token $token, UserName $name): self
    {
        return new self(
            $id,
            $externalUserId,
            $token,
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

    public function getExternalUserId(): ExternalUserId
    {
        return $this->externalUserId;
    }

    public function getToken(): Token
    {
        return $this->token;
    }

    public function getName(): UserName
    {
        return $this->name;
    }
}
