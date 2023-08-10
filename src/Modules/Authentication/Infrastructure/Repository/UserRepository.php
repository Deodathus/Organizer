<?php
declare(strict_types=1);

namespace App\Modules\Authentication\Infrastructure\Repository;

use App\Modules\Authentication\Domain\Entity\User;
use App\Modules\Authentication\Domain\Exception\UserDoesNotExist;
use App\Modules\Authentication\Domain\Repository\UserRepository as UserRepositoryInterface;
use App\Modules\Authentication\Domain\ValueObject\RefreshToken;
use App\Modules\Authentication\Domain\ValueObject\Token;
use App\Modules\Authentication\Domain\ValueObject\UserExternalId;
use App\Modules\Authentication\Domain\ValueObject\UserId;
use App\Modules\Authentication\Domain\ValueObject\UserName;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Psr\Log\LoggerInterface;

final class UserRepository implements UserRepositoryInterface
{
    private const DB_TABLE_NAME = 'users';

    public function __construct(
        private readonly Connection $connection,
        private readonly LoggerInterface $logger
    ) {}

    public function register(User $user): void
    {
        try {
            $this->connection
                ->createQueryBuilder()
                ->insert(self::DB_TABLE_NAME)
                ->values([
                    'id' => ':id',
                    'external_id' => ':externalId',
                    'first_name' => ':firstName',
                    'last_name' => ':lastName',
                    'api_token' => ':apiToken',
                    'api_refresh_token' => ':refreshToken',
                ])
                ->setParameters([
                    'id' => $user->getUserId()->toString(),
                    'externalId' => $user->getExternalUserId()->toString(),
                    'firstName' => $user->getName()->firstName,
                    'lastName' => $user->getName()->lastName,
                    'apiToken' => $user->getToken()->value,
                    'refreshToken' => $user->getRefreshToken()->value,
                ])
                ->executeStatement();
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());

            throw $exception;
        }
    }

    public function fetchByToken(Token $token): User
    {
        $userData = $this->getFetchUserDataQueryBuilder()
            ->where('u.api_token = :apiToken')
            ->setParameter('apiToken', $token->value)
            ->fetchAssociative();

        if ($userData) {
            return User::reproduce(
                UserId::fromString($userData['id']),
                UserExternalId::fromString($userData['external_id']),
                new Token($userData['api_token']),
                new RefreshToken($userData['api_refresh_token']),
                new UserName($userData['first_name'], $userData['last_name'])
            );
        }

        throw UserDoesNotExist::withToken($token->value);
    }

    public function fetchByExternalId(UserExternalId $externalUserId): User
    {
        $userData = $this->getFetchUserDataQueryBuilder()
            ->where('u.external_id = :externalId')
            ->setParameter('externalId', $externalUserId->toString())
            ->fetchAssociative();

        if ($userData) {
            return User::reproduce(
                UserId::fromString($userData['id']),
                UserExternalId::fromString($userData['external_id']),
                new Token($userData['api_token']),
                new RefreshToken($userData['api_refresh_token']),
                new UserName($userData['first_name'], $userData['last_name'])
            );
        }

        throw UserDoesNotExist::withExternalId($externalUserId->toString());
    }

    private function getFetchUserDataQueryBuilder(): QueryBuilder
    {
        return $this->connection
            ->createQueryBuilder()
            ->select('u.id, u.external_id, u.api_token, u.api_refresh_token, u.first_name, u.last_name')
            ->from(self::DB_TABLE_NAME, 'u');
    }
}
