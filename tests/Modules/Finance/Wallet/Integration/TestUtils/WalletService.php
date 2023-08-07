<?php

declare(strict_types=1);

namespace App\Tests\Modules\Finance\Wallet\Integration\TestUtils;

use App\Modules\Finance\Currency\Domain\Entity\Currency;
use App\Modules\Finance\Currency\Domain\Repository\CurrencyRepository;
use App\Modules\Finance\Currency\Domain\ValueObject\CurrencyCode;
use App\Modules\Finance\Wallet\Application\Exception\WalletDoesNotExistException;
use App\Modules\Finance\Wallet\Application\Exception\WalletWithoutOwnersException;
use App\Modules\Finance\Wallet\Domain\Entity\Wallet;
use App\Modules\Finance\Wallet\Domain\Entity\WalletOwner;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletBalance;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletCurrencyId;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletId;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletOwnerExternalId;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletOwnerId;
use Doctrine\DBAL\Connection;
use Money\Money;

final readonly class WalletService
{
    private const WALLET_DB_TABLE_NAME = 'wallets';
    private const WALLET_OWNERS_DB_TABLE_NAME = 'wallet_owners';
    private const CURRENCY_DB_TABLE_NAME = 'currencies';

    public function __construct(
        private CurrencyRepository $currencyRepository,
        private Connection $connection
    ) {}

    public function storeCurrency(CurrencyCode $code): Currency
    {
        $currency = Currency::create($code);

        $this->currencyRepository->store($currency);

        return $currency;
    }

    public function fetchWalletById(WalletId $walletId): Wallet
    {
        $rawData = $this->connection->createQueryBuilder()
            ->select('id', 'name', 'balance', 'currency_id', )
            ->from(self::WALLET_DB_TABLE_NAME, 'w')
            ->where('w.id = :id')
            ->setParameter('id', $walletId->toString())
            ->fetchAssociative();

        if (!$rawData) {
            throw WalletDoesNotExistException::withId($walletId->toString());
        }

        $ownersRawIds = $this->connection->createQueryBuilder()
            ->select('id', 'external_id')
            ->from(self::WALLET_OWNERS_DB_TABLE_NAME)
            ->where('wallet_id = :walletId')
            ->setParameter('walletId', $walletId->toString())
            ->fetchAllAssociative();

        if (!$ownersRawIds) {
            throw WalletWithoutOwnersException::withId($walletId->toString());
        }

        $owners = [];
        foreach ($ownersRawIds as $ownerRawId) {
            $owners[] = WalletOwner::reproduce(
                WalletOwnerId::fromString($ownerRawId['id']),
                WalletOwnerExternalId::fromString($ownerRawId['external_id'])
            );
        }

        $currencyCode = $this->connection->createQueryBuilder()
            ->select('code')
            ->from(self::CURRENCY_DB_TABLE_NAME)
            ->where('id = :id')
            ->setParameter('id', $rawData['currency_id'])
            ->fetchOne();

        return Wallet::reproduce(
            WalletId::fromString($rawData['id']),
            $rawData['name'],
            $owners,
            new WalletBalance(new Money($rawData['balance'], new \Money\Currency($currencyCode))),
            WalletCurrencyId::fromString($rawData['currency_id'])
        );
    }
}
