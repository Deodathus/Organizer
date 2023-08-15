<?php

declare(strict_types=1);

namespace App\Tests\Modules\Authentication\TestDoubles;

use App\Modules\Authentication\Domain\Entity\User;
use App\Modules\Authentication\Domain\Exception\UserDoesNotExist;
use App\Modules\Authentication\Domain\Repository\UserRepository;
use App\Modules\Authentication\Domain\ValueObject\Token;
use App\Modules\Authentication\Domain\ValueObject\UserExternalId;

final class UserRepositoryFake implements UserRepository
{
    /** @var User[] $registeredUsers */
    private array $registeredUsers = [];

    public function register(User $user): void
    {
        $this->registeredUsers[$user->getExternalUserId()->toString()] = $user;
    }

    public function fetchByToken(Token $token): User
    {
        foreach ($this->registeredUsers as $user) {
            if ($user->getToken()->value === $token->value) {
                return $user;
            }
        }

        throw UserDoesNotExist::withToken($token->value);
    }

    public function fetchByExternalId(UserExternalId $externalUserId): User
    {
        foreach ($this->registeredUsers as $user) {
            if ($user->getExternalUserId()->toString() === $externalUserId->toString()) {
                return $user;
            }
        }

        throw UserDoesNotExist::withToken($externalUserId->toString());
    }
}
