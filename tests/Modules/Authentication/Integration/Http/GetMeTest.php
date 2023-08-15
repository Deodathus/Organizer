<?php

declare(strict_types=1);

namespace App\Tests\Modules\Authentication\Integration\Http;

use App\Tests\SharedInfrastructure\IntegrationTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/** @group integration */
final class GetMeTest extends IntegrationTestBase
{
    private const API_URL = '/api/auth/me';

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpAuthUserProvider();
    }

    /** @test */
    public function shouldReturnUser(): void
    {
        // act
        $this->client->request(
            Request::METHOD_GET,
            self::API_URL . $this->getAuthString()
        );

        // assert
        $response = $this->client->getResponse();
        $responseContent = json_decode($response->getContent(), false, 512, JSON_THROW_ON_ERROR);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        self::assertSame($this->token->toString(), $responseContent->me->token);
    }
}
