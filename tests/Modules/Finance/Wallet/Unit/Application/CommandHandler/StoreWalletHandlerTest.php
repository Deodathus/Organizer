<?php
declare(strict_types=1);

namespace App\Tests\Modules\Finance\Wallet\Unit\Application\CommandHandler;

use App\Modules\Authentication\ModuleAPI\Application\Query\FetchUserIdByToken;
use App\Modules\Finance\Currency\ModuleAPI\Application\Query\FetchCurrencyByCode;
use App\Modules\Finance\Wallet\Application\Command\StoreWallet;
use App\Modules\Finance\Wallet\Application\CommandHandler\StoreWalletHandler;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletId;
use App\Tests\Modules\Finance\Wallet\Unit\TestDoubles\QueryHandler\FetchCurrencyByCodeHandlerStub;
use App\Tests\Modules\Finance\Wallet\Unit\TestDoubles\QueryHandler\FetchUserIdByTokenHandlerStub;
use App\Tests\Modules\Finance\Wallet\Unit\TestDoubles\Service\WalletPersisterFake;
use App\Tests\SharedInfrastructure\Unit\Application\Messenger\QueryBusFake;
use Monolog\Test\TestCase;
use Ramsey\Uuid\Uuid;

final class StoreWalletHandlerTest extends TestCase
{
    private const WALLET_NAME = 'Test wallet';
    private const CREATOR_API_TOKEN = 'token';
    private const CURRENCY_CODE = 'PLN';
    private const UNKNOWN_CURRENCY_CODE = 'xxx';

    private const START_BALANCE = 100;

    /** @test */
    public function shouldStoreWallet(): void
    {
        // arrange
        $currencyId = Uuid::uuid4()->toString();
        $userId = Uuid::uuid4()->toString();

        $walletPersister = new WalletPersisterFake();
        $queryBus = new QueryBusFake();
        $queryBus->addHandler(FetchCurrencyByCode::class, new FetchCurrencyByCodeHandlerStub($currencyId));
        $queryBus->addHandler(FetchUserIdByToken::class, new FetchUserIdByTokenHandlerStub($userId));

        $sut = new StoreWalletHandler(
            $walletPersister,
            $queryBus
        );

        // act
        $createdWallet = ($sut)(
            new StoreWallet(
            self::WALLET_NAME,
            self::CREATOR_API_TOKEN,
            self::CURRENCY_CODE,
            self::START_BALANCE
            )
        );

        // assert
        $persistedWalled = $walletPersister->findPersisted(WalletId::fromString($createdWallet->walletId));
        $this->assertSame(self::WALLET_NAME, $persistedWalled->name);
        $this->assertSame(self::CURRENCY_CODE, $persistedWalled->currencyCode);
        $this->assertSame(self::START_BALANCE, $persistedWalled->startBalance);
        $this->assertSame($currencyId, $persistedWalled->currencyId);
        $this->assertSame($userId, $persistedWalled->creatorId);
    }

    public function shouldNotStoreWalletBecauseCurrencyDoesNotExist(): void
    {

    }

    public function shouldNotStoreWalletBecauseCreatorIdentityDoesNotExist(): void
    {

    }
}
