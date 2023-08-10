<?php
declare(strict_types=1);

namespace App\Tests\Modules\Finance\Currency\Integration\Http;

use App\Modules\Finance\Currency\Application\Exception\CurrencyDoesNotExistException;
use App\Modules\Finance\Currency\Domain\ValueObject\CurrencyCode;
use App\Tests\Modules\Finance\Currency\Integration\TestUtils\CurrencyService;
use App\Tests\SharedInfrastructure\IntegrationTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/** @group integration */
final class DeleteCurrencyTest extends IntegrationTestBase
{
    private const API_URL = '/api/finance/currency/%s';
    private const PLN_CURRENCY_CODE = 'PLN';
    private CurrencyService $currencyService;

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpAuthUserProvider();

        $this->currencyService = $this->container->get(CurrencyService::class);
    }

    /** @test */
    public function shouldDeleteCurrency(): void
    {
        // arrange
        $currency = $this->currencyService->storeCurrency(CurrencyCode::from(self::PLN_CURRENCY_CODE));

        // act
        $this->client->request(
            Request::METHOD_DELETE,
            sprintf(self::API_URL, $currency->getId()->toString()) . $this->getAuthString()
        );

        // assert
        $this->assertSame(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());

        $this->expectException(CurrencyDoesNotExistException::class);
        $this->currencyService->fetchCurrencyById($currency->getId());
    }
}
