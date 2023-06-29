<?php
declare(strict_types=1);

namespace App\Modules\Authentication\Infrastructure\Repository;

use App\Modules\Authentication\Domain\Entity\User;
use App\Modules\Authentication\Domain\Repository\UserRepository as UserRepositoryInterface;
use App\Modules\Authentication\Domain\ValueObject\Token;
use Doctrine\DBAL\Connection;

final class UserRepository implements UserRepositoryInterface
{
    private const DB_TABLE_NAME = 'users';

    public function __construct(
        private readonly Connection $connection
    ) {}

    public function register(User $user): void
    {

    }

    public function fetchByToken(Token $token): void
    {
        // TODO: Implement fetchByToken() method.
    }
}
