<?php
declare(strict_types=1);

namespace App\Tests\SharedInfrastructure;

use App\Modules\Authentication\Application\DTO\ExternalUserDTO;
use App\Modules\Authentication\Application\Repository\ExternalUserRepository;
use App\Tests\SharedInfrastructure\TestDoubles\ExternalUserRepositoryFake;
use Doctrine\DBAL\Connection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class IntegrationTestBase extends WebTestCase
{
    protected const TOKEN_HEADER = 'X-Auth-Token';
    protected UuidInterface $token;
    protected UuidInterface $externalUserId;
    protected UuidInterface $refreshToken;
    protected KernelBrowser $client;
    protected ContainerInterface $container;
    private Connection $connection;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->container = self::getContainer();

        $this->connection = $this->container->get(Connection::class);

        $this->clearDatabase();

        $this->externalUserId = Uuid::uuid4();
        $this->token = Uuid::uuid4();
        $this->refreshToken = Uuid::uuid4();

        $this->setUpUser();
    }

    protected function getAuthString(): string
    {
        return sprintf('?%s=%s', self::TOKEN_HEADER, $this->token);
    }

    protected function setUpAuthUserProvider(): void
    {
        $this->container->set(
            ExternalUserRepository::class,
            new ExternalUserRepositoryFake([
                new ExternalUserDTO(
                    $this->externalUserId->toString(),
                    $this->token->toString(),
                    $this->refreshToken->toString()
                ),
            ])
        );
    }

    private function setUpUser(): void
    {
        $this->connection
            ->createQueryBuilder()
            ->insert('users')
            ->values([
                'id' => ':id',
                'external_id' => ':externalId',
                'first_name' => ':firstName',
                'last_name' => ':lastName',
                'api_token' => ':apiToken',
                'api_refresh_token' => ':apiRefreshToken',
            ])
            ->setParameters([
                'id' => Uuid::uuid4(),
                'externalId' => $this->externalUserId,
                'firstName' => 'Test',
                'lastName' => 'Test',
                'apiToken' => $this->token,
                'apiRefreshToken' => $this->refreshToken,
            ])
            ->executeStatement();
    }

    private function clearDatabase(): void
    {
        $this->connection->executeStatement('truncate currencies');
        $this->connection->executeStatement('truncate users');
    }
}
