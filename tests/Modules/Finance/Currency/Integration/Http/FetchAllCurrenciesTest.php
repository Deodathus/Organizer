<?php

declare(strict_types=1);

namespace App\Tests\Modules\Finance\Currency\Integration\Http;

use App\Modules\Finance\Currency\Domain\ValueObject\CurrencyCode;
use App\Tests\Modules\Finance\Currency\Integration\TestUtils\CurrencyService;
use App\Tests\SharedInfrastructure\IntegrationTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group development
 * @group integration
 */
final class FetchAllCurrenciesTest extends IntegrationTestBase
{
    private const API_URL = '/api/finance/currency';
    private CurrencyService $currencyService;

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpAuthUserProvider();

        /** @var CurrencyService $currencyService */
        $currencyService = $this->container->get(CurrencyService::class);
        $this->currencyService = $currencyService;
    }

    /** @test */
    public function shouldNotFetchWalletsWithUnauthorizedUser(): void
    {
        // act
        $this->client->request(
            Request::METHOD_GET,
            self::API_URL
        );

        // assert
        self::assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    /** @test */
    public function shouldFetchAllCurrencies(): void
    {
        // arrange
        $this->currencyService->storeCurrency(CurrencyCode::EUR);
        $this->currencyService->storeCurrency(CurrencyCode::PLN);

        // act
        $this->client->request(
            Request::METHOD_GET,
            self::API_URL,
            server: $this->getAuthHeader()
        );

        // assert
        $response = $this->client->getResponse();
        /** @var string $result */
        $result = $response->getContent();

        /** @var object{items: array<object{id: string, code: string}>} $fetchedCurrencies */
        $fetchedCurrencies = json_decode($result, false, 512, JSON_THROW_ON_ERROR);
        [$firstCurrency, $secondCurrency] = $fetchedCurrencies->items;

        self::assertSame(CurrencyCode::EUR->value, $firstCurrency->code);
        self::assertSame(CurrencyCode::PLN->value, $secondCurrency->code);
    }

    /** @test */
    public function shouldReturnEmptyCollection(): void
    {
        // act
        $this->client->request(
            Request::METHOD_GET,
            self::API_URL,
            server: $this->getAuthHeader()
        );

        // assert
        $response = $this->client->getResponse();
        /** @var string $result */
        $result = $response->getContent();
        /** @var object{items: array<empty>} $fetchedCurrencies */
        $fetchedCurrencies = json_decode($result, false, 512, JSON_THROW_ON_ERROR);

        self::assertEmpty($fetchedCurrencies->items);
    }
}
