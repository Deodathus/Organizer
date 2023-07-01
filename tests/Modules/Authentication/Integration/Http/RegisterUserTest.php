<?php
declare(strict_types=1);

namespace App\Tests\Modules\Authentication\Integration\Http;

use App\Modules\Authentication\Application\DTO\ExternalUserDTO;
use App\Modules\Authentication\Application\Service\ExternalUserRepository;
use App\Tests\Modules\Authentication\Integration\TestDoubles\ExternalUserRepositoryFake;
use App\Tests\Modules\Authentication\Integration\TestUtils\UserService;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/** @group integration */
final class RegisterUserTest extends WebTestCase
{
    private const API_URL = '/api/auth/user';
    /** @var UserService $userService */
    private UserService $userService;
    private KernelBrowser $client;
    private ContainerInterface $container;

    public function setUp(): void
    {
        $this->client = self::createClient();

        $this->container = self::getContainer();
        $this->userService = $this->container->get(UserService::class);
    }

    /** @test */
    public function shouldRegisterUser(): void
    {
        $externalUserId = Uuid::uuid4();
        $token = Uuid::uuid4();
        $refreshToken = Uuid::uuid4();

        $this->container->set(
            ExternalUserRepository::class,
            new ExternalUserRepositoryFake([
                new ExternalUserDTO($externalUserId->toString(), $token->toString(), $refreshToken->toString()),
            ])
        );

        $this->client->request(
            Request::METHOD_POST,
            self::API_URL,
            content: json_encode(
                [
                    'userId' => $externalUserId->toString(),
                ],
                JSON_THROW_ON_ERROR
            )
        );

        $registeredUser = $this->userService->fetchUserByExternalId($externalUserId->toString());
        $this->assertNotNull($registeredUser);
        $this->assertSame($token->toString(), $registeredUser->getToken()->value);
        $this->assertSame($refreshToken->toString(), $registeredUser->getRefreshToken()->value);
    }

    /** @test */
    public function shouldReturnNotFoundBecauseOfExternalUserDoesNotExist(): void
    {
        $externalUserId = Uuid::uuid4();

        $this->container->set(
            ExternalUserRepository::class,
            new ExternalUserRepositoryFake([])
        );

        $this->client->request(
            Request::METHOD_POST,
            self::API_URL,
            content: json_encode(
                [
                    'userId' => $externalUserId->toString(),
                ],
                JSON_THROW_ON_ERROR
            )
        );

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }
}
