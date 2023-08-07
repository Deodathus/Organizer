<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Infrastructure\Repository;

use App\Modules\Finance\Wallet\Application\Exception\WalletDoesNotExistException;
use App\Modules\Finance\Wallet\Application\Exception\WalletWithoutOwnersException;
use App\Modules\Finance\Wallet\Domain\Entity\Wallet;
use App\Modules\Finance\Wallet\Domain\Entity\WalletOwner;
use App\Modules\Finance\Wallet\Domain\Repository\WalletRepository as WalletRepositoryInterface;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletBalance;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletCurrency;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletCurrencyId;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletOwnerExternalId;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletOwnerId;
use App\Shared\Domain\ValueObject\WalletId;
use Doctrine\DBAL\Connection;
use Money\Currency;
use Money\Money;

final class WalletRepository implements WalletRepositoryInterface
{
    private const DB_TABLE_NAME = 'wallets';
    private const WALLET_OWNERS_DB_TABLE_NAME = 'wallet_owners';

    public function __construct(
        private readonly Connection $connection
    ) {}

    public function store(Wallet $wallet): void
    {
        $this->connection
            ->createQueryBuilder()
            ->insert(self::DB_TABLE_NAME)
            ->values([
                'id' => ':id',
                'name' => ':name',
                'balance' => ':balance',
                'currency_id' => ':currencyId',
                'currency_code' => ':currencyCode',
            ])
            ->setParameters([
                'id' => $wallet->getId()->toString(),
                'name' => $wallet->getName(),
                'balance' => $wallet->getBalance()->toString(),
                'currencyId' => $wallet->getCurrencyId()->toString(),
                'currencyCode' => $wallet->getCurrencyCode(),
            ])
            ->executeStatement();

        foreach ($wallet->getOwners() as $walletOwner) {
            $this->connection
                ->createQueryBuilder()
                ->insert(self::WALLET_OWNERS_DB_TABLE_NAME)
                ->values([
                    'id' => ':id',
                    'external_id' => ':externalId',
                    'wallet_id' => ':walletId',
                ])
                ->setParameters([
                    'id' => $walletOwner->ownerId->toString(),
                    'externalId' => $walletOwner->externalId->toString(),
                    'walletId' => $wallet->getId()->toString(),
                ])
                ->executeStatement();
        }
    }

    public function fetchById(WalletId $walletId): Wallet
    {
        $rawData = $this->connection->createQueryBuilder()
            ->select('id', 'name', 'balance', 'currency_id', 'currency_code')
            ->from(self::DB_TABLE_NAME, 'w')
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

        return Wallet::reproduce(
            WalletId::fromString($rawData['id']),
            $rawData['name'],
            $owners,
            new WalletBalance(new Money($rawData['balance'], new Currency($rawData['currency_code']))),
            new WalletCurrency(
                WalletCurrencyId::fromString($rawData['currency_id']),
                $rawData['currency_code']
            )
        );
    }
}
