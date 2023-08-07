<?php

declare(strict_types=1);

namespace App\Tests\Modules\Finance\Wallet\Unit\Application\Service;

use App\Modules\Finance\Wallet\Application\Service\WalletPersister;
use App\Tests\Modules\Finance\Wallet\Unit\TestDoubles\Mother\WalletDTOMother;
use App\Tests\Modules\Finance\Wallet\Unit\TestDoubles\Repository\WalletRepositoryFake;
use PHPUnit\Framework\TestCase;

final class WalletPersisterTest extends TestCase
{
    /** @test */
    public function shouldStoreWallet(): void
    {
        // arrange
        $walletRepository = new WalletRepositoryFake();
        $sut = new WalletPersister($walletRepository);

        // act
        $walletId = $sut->persist(WalletDTOMother::createWithDefaults());

        // assert
        $storedWallet = $walletRepository->fetchById($walletId);

        $this->assertSame(WalletDTOMother::NAME, $storedWallet->getName());
        $this->assertSame(WalletDTOMother::START_BALANCE, (int) $storedWallet->getBalance()->value->getAmount());
        $this->assertSame(
            WalletDTOMother::CURRENCY_CODE,
            $storedWallet->getBalance()->value->getCurrency()->getCode()
        );
        $this->assertSame(WalletDTOMother::CURRENCY_ID, $storedWallet->getCurrencyId()->toString());
    }
}
