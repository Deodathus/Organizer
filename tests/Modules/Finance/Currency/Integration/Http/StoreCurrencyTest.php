<?php

declare(strict_types=1);

namespace App\Tests\Modules\Finance\Currency\Integration\Http;

use App\Modules\Finance\Currency\Domain\ValueObject\CurrencyCode;
use App\Modules\Finance\Currency\Domain\ValueObject\CurrencyId;
use App\Tests\Modules\Finance\Currency\Integration\TestUtils\CurrencyService;
use App\Tests\SharedInfrastructure\IntegrationTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/** @group integration */
final class StoreCurrencyTest extends IntegrationTestBase
{
    private const API_URL = '/api/finance/currency';
    private const PLN_CURRENCY_CODE = 'PLN';
    private const CAD_CURRENCY_CODE = 'CAD';
    private const UNKNOWN_CURRENCY_CODE = 'xxx';
    private CurrencyService $currencyService;

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpAuthUserProvider();

        $this->currencyService = $this->container->get(CurrencyService::class);
    }

    /** @test */
    public function shouldNotStoreCurrencyWithUnauthorizedUser(): void
    {
        // arrange
        // act
        $this->client->request(
            Request::METHOD_POST,
            self::API_URL,
            content: json_encode([
                'code' => self::PLN_CURRENCY_CODE,
            ], JSON_THROW_ON_ERROR)
        );

        $response = $this->client->getResponse();

        // assert
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /**
     * @test
     *
     * @dataProvider supportedCurrenciesDataProvider
     */
    public function shouldStoreCurrency(string $currencyCode): void
    {
        // arrange
        // act
        $this->client->request(
            Request::METHOD_POST,
            self::API_URL . $this->getAuthString(),
            content: json_encode([
                'code' => $currencyCode,
            ], JSON_THROW_ON_ERROR)
        );

        $response = $this->client->getResponse();

        // assert
        $createdCurrencyId = json_decode($response->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $createdCurrency = $this->currencyService->fetchCurrencyById(CurrencyId::fromString($createdCurrencyId->id));

        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertNotNull(
            $createdCurrency
        );
        $this->assertSame($currencyCode, $createdCurrency->code);
    }

    /** @test */
    public function shouldNotStoreCurrencyIfCodeAlreadyTaken(): void
    {
        // arrange
        $this->currencyService->storeCurrency(CurrencyCode::PLN);

        // act
        $this->client->request(
            Request::METHOD_POST,
            self::API_URL . $this->getAuthString(),
            content: json_encode([
                'code' => self::PLN_CURRENCY_CODE,
            ], JSON_THROW_ON_ERROR)
        );

        $response = $this->client->getResponse();

        // assert
        $this->assertSame(Response::HTTP_CONFLICT, $response->getStatusCode());
    }

    /** @test */
    public function shouldNotStoreCurrencyWithUnknownCode(): void
    {
        // arrange
        // act
        $this->client->request(
            Request::METHOD_POST,
            self::API_URL . $this->getAuthString(),
            content: json_encode([
                'code' => self::UNKNOWN_CURRENCY_CODE,
            ], JSON_THROW_ON_ERROR)
        );

        $response = $this->client->getResponse();

        // assert
        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @return array<array<string>>
     */
    public function supportedCurrenciesDataProvider(): array
    {
        return [
            [self::PLN_CURRENCY_CODE],
            [self::CAD_CURRENCY_CODE],
        ];
    }
}
