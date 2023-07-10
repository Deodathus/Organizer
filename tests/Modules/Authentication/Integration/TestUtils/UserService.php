<?php
declare(strict_types=1);

namespace App\Tests\Modules\Authentication\Integration\TestUtils;

use App\Modules\Authentication\Domain\Entity\User;
use App\Modules\Authentication\Domain\Repository\UserRepository;
use App\Modules\Authentication\Domain\ValueObject\UserExternalId;

final class UserService
{
    public function __construct(private readonly UserRepository $userRepository) {}

    public function fetchUserByExternalId(string $externalId): User
    {
        return $this->userRepository->fetchByExternalId(UserExternalId::fromString($externalId));
    }
}
