<?php

declare(strict_types=1);

namespace App\Tests\Modules\Finance\Wallet\Integration\Http;

use App\Modules\Finance\Currency\ModuleAPI\Application\Enum\SupportedCurrencies;
use App\Shared\Domain\ValueObject\WalletId;
use App\Tests\Modules\Finance\Wallet\Integration\TestUtils\WalletService;
use App\Tests\Modules\Finance\Wallet\Unit\TestDoubles\Service\MoneyAmountNormalizer;
use App\Tests\SharedInfrastructure\IntegrationTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/** @group integration */
final class StoreWalletTest extends IntegrationTestBase
{
    private const API_URL = '/api/finance/wallet';
    private const BALANCE = '100';
    private const DECIMAL_BALANCE = '100.25';
    private const WALLET_NAME = 'Test wallet';
    private const WALLET_CURRENCY_CODE = SupportedCurrencies::PLN;
    private const UNKNOWN_CURRENCY_CODE = 'xxx';
    private const NON_EXISTING_CURRENCY_CODE = SupportedCurrencies::USD;
    private WalletService $walletService;

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpAuthUserProvider();

        /** @var WalletService $walletService */
        $walletService = $this->container->get(WalletService::class);
        $this->walletService = $walletService;
    }

    /** @test */
    public function shouldNotStoreWalletWithUnauthorizedUser(): void
    {
        // act
        $this->client->request(
            Request::METHOD_POST,
            self::API_URL,
            content: json_encode([
                'name' => self::WALLET_NAME,
                'balance' => self::BALANCE,
                'currencyCode' => self::WALLET_CURRENCY_CODE,
            ], JSON_THROW_ON_ERROR)
        );

        // assert
        $response = $this->client->getResponse();

        self::assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /** @test */
    public function shouldStoreWallet(): void
    {
        // arrange
        $currency = $this->walletService->storeCurrency(SupportedCurrencies::PLN);

        // act
        $this->client->request(
            Request::METHOD_POST,
            self::API_URL . $this->getAuthString(),
            server: $this->getAuthHeader(),
            content: json_encode([
                'name' => self::WALLET_NAME,
                'balance' => self::BALANCE,
                'currencyCode' => self::WALLET_CURRENCY_CODE,
            ], JSON_THROW_ON_ERROR)
        );

        // assert
        $response = $this->client->getResponse();
        /** @var string $responseContent */
        $responseContent = $response->getContent();
        /** @var object{id: string} $parsedJson */
        $parsedJson = json_decode(
            $responseContent,
            false,
            512,
            JSON_THROW_ON_ERROR
        );
        $createdWallet = $this->walletService->fetchWalletById(
            WalletId::fromString($parsedJson->id)
        );

        self::assertSame(Response::HTTP_CREATED, $response->getStatusCode());
        self::assertSame(self::WALLET_NAME, $createdWallet->getName());
        self::assertSame(
            self::BALANCE,
            (string) MoneyAmountNormalizer::normalize((int) $createdWallet->getBalance()->toString())
        );
        self::assertSame($currency->getId()->toString(), $createdWallet->getCurrencyId()->toString());
    }

    /** @test */
    public function shouldStoreWalletWithDecimalBalance(): void
    {
        // arrange
        $currency = $this->walletService->storeCurrency(SupportedCurrencies::PLN);

        // act
        $this->client->request(
            Request::METHOD_POST,
            self::API_URL . $this->getAuthString(),
            server: $this->getAuthHeader(),
            content: json_encode([
                'name' => self::WALLET_NAME,
                'balance' => self::DECIMAL_BALANCE,
                'currencyCode' => self::WALLET_CURRENCY_CODE,
            ], JSON_THROW_ON_ERROR)
        );

        // assert
        $response = $this->client->getResponse();
        /** @var string $responseContent */
        $responseContent = $response->getContent();
        /** @var object{id: string} $parsedJson */
        $parsedJson = json_decode(
            $responseContent,
            false,
            512,
            JSON_THROW_ON_ERROR
        );
        $createdWallet = $this->walletService->fetchWalletById(
            WalletId::fromString($parsedJson->id)
        );

        self::assertSame(Response::HTTP_CREATED, $response->getStatusCode());
        self::assertSame(self::WALLET_NAME, $createdWallet->getName());
        self::assertSame(
            self::DECIMAL_BALANCE,
            (string) MoneyAmountNormalizer::normalize((int) $createdWallet->getBalance()->toString())
        );
        self::assertSame($currency->getId()->toString(), $createdWallet->getCurrencyId()->toString());
    }

    /** @test */
    public function shouldNotStoreWalletBecauseCurrencyDoesNotExist(): void
    {
        // act
        $this->client->request(
            Request::METHOD_POST,
            self::API_URL . $this->getAuthString(),
            server: $this->getAuthHeader(),
            content: json_encode([
                'name' => self::WALLET_NAME,
                'balance' => self::BALANCE,
                'currencyCode' => self::NON_EXISTING_CURRENCY_CODE,
            ], JSON_THROW_ON_ERROR)
        );

        // assert
        $response = $this->client->getResponse();
        self::assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /** @test */
    public function shouldNotStoreWalletBecauseCurrencyIsNotSupported(): void
    {
        // act
        $this->client->request(
            Request::METHOD_POST,
            self::API_URL . $this->getAuthString(),
            server: $this->getAuthHeader(),
            content: json_encode([
                'name' => self::WALLET_NAME,
                'balance' => self::BALANCE,
                'currencyCode' => self::UNKNOWN_CURRENCY_CODE,
            ], JSON_THROW_ON_ERROR)
        );

        // assert
        $response = $this->client->getResponse();
        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}
