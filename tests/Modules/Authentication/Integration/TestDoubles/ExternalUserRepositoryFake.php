<?php
declare(strict_types=1);

namespace App\Tests\Modules\Authentication\Integration\TestDoubles;

use App\Modules\Authentication\Application\DTO\ExternalUserDTO;
use App\Modules\Authentication\Application\Exception\ExternalUserDoesNotExist;
use App\Modules\Authentication\Application\Service\ExternalUserRepository;

final class ExternalUserRepositoryFake implements ExternalUserRepository
{
    /** @param ExternalUserDTO[] $users */
    public function __construct(
        private readonly array $users
    ) {}

    public function fetchById(string $externalUserId): ExternalUserDTO
    {
        foreach ($this->users as $user) {
            if ($user->externalId === $externalUserId) {
                return $user;
            }
        }

        throw ExternalUserDoesNotExist::withId($externalUserId);
    }
}
