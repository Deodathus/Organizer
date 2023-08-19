<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Infrastructure\Repository;

use App\Modules\Finance\Wallet\Application\Exception\WalletDoesNotExistException;
use App\Modules\Finance\Wallet\Application\Exception\WalletWithoutOwnersException;
use App\Modules\Finance\Wallet\Application\Service\WalletBalanceCreator;
use App\Modules\Finance\Wallet\Domain\Entity\Wallet;
use App\Modules\Finance\Wallet\Domain\Entity\WalletOwner;
use App\Modules\Finance\Wallet\Domain\Exception\WalletDoesNotExist;
use App\Modules\Finance\Wallet\Domain\Repository\TransactionRepository;
use App\Modules\Finance\Wallet\Domain\Repository\WalletRepository as WalletRepositoryInterface;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletCurrency;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletCurrencyId;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletOwnerExternalId;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletOwnerId;
use App\Shared\Domain\ValueObject\WalletId;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;

final class WalletRepository implements WalletRepositoryInterface
{
    private const DB_TABLE_NAME = 'wallets';
    private const WALLET_OWNERS_DB_TABLE_NAME = 'wallet_owners';

    public function __construct(
        private readonly Connection $connection,
        private readonly TransactionRepository $transactionRepository,
        private readonly WalletBalanceCreator $walletBalanceCreator
    ) {
    }

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
        /** @var array{id: string, name: string, balance: string, currency_id: string, currency_code: string}|false $rawData */
        $rawData = $this->connection->createQueryBuilder()
            ->select('id', 'name', 'balance', 'currency_id', 'currency_code')
            ->from(self::DB_TABLE_NAME, 'w')
            ->where('w.id = :id')
            ->setParameter('id', $walletId->toString())
            ->fetchAssociative();

        if (!$rawData) {
            throw WalletDoesNotExistException::withId($walletId->toString());
        }

        /** @var array<int, array{id: string, external_id: string}> $ownersRawIds */
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

        $walletCurrency = new WalletCurrency(
            WalletCurrencyId::fromString($rawData['currency_id']),
            $rawData['currency_code']
        );

        $transactions = $this->transactionRepository->fetchTransactionsByWallet($walletId, $walletCurrency);

        $wallet = Wallet::reproduce(
            $walletId,
            $rawData['name'],
            $owners,
            $this->walletBalanceCreator->create($rawData['balance'], $walletCurrency->currencyCode),
            $walletCurrency
        );

        foreach ($transactions as $transaction) {
            $wallet->registerTransaction($transaction);
        }

        return $wallet;
    }

    public function fetchByOwnerExternalId(WalletOwnerExternalId $ownerId, int $perPage = 10, int $page = 1): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $walletsIds = array_keys($this->connection->createQueryBuilder()
            ->select('wo.wallet_id')
            ->from(self::WALLET_OWNERS_DB_TABLE_NAME, 'wo')
            ->where('external_id = :externalId')
            ->setParameter('externalId', $ownerId->toString())
            ->setMaxResults($perPage)
            ->setFirstResult($page * $perPage - $perPage)
            ->fetchAllAssociativeIndexed());

        /** @var array<int, array{id: string, name: string, balance: string, currency_id: string, currency_code: string}> $rawData */
        $rawData = $this->connection->createQueryBuilder()
            ->select('w.id', 'name', 'balance', 'currency_id', 'currency_code')
            ->from(self::DB_TABLE_NAME, 'w')
            ->where(
                $queryBuilder->expr()->in('w.id', ':walletsIds')
            )
            ->setParameter('walletsIds', $walletsIds, ArrayParameterType::STRING)
            ->fetchAllAssociative();

        /** @var array<int, array{id: string, external_id: string, wallet_id: string}> $ownersRawIds */
        $ownersRawIds = $this->connection->createQueryBuilder()
            ->select('wo.id', 'wo.external_id', 'wallet_id')
            ->from(self::WALLET_OWNERS_DB_TABLE_NAME, 'wo')
            ->where(
                $queryBuilder->expr()->in('wallet_id', ':walletsIds')
            )
            ->setParameter('walletsIds', $walletsIds, ArrayParameterType::STRING)
            ->fetchAllAssociative();

        $owners = [];
        foreach ($ownersRawIds as $ownerRawId) {
            $owners[$ownerRawId['wallet_id']][] = WalletOwner::reproduce(
                WalletOwnerId::fromString($ownerRawId['id']),
                WalletOwnerExternalId::fromString($ownerRawId['external_id'])
            );
        }

        $wallets = [];
        foreach ($rawData as $rawWalletData) {
            $walletId = WalletId::fromString($rawWalletData['id']);
            $walletCurrency = new WalletCurrency(
                WalletCurrencyId::fromString($rawWalletData['currency_id']),
                $rawWalletData['currency_code']
            );

            $transactions = $this->transactionRepository->fetchTransactionsByWallet(
                $walletId,
                $walletCurrency
            );

            $wallet = Wallet::reproduce(
                $walletId,
                $rawWalletData['name'],
                $owners[$rawWalletData['id']],
                $this->walletBalanceCreator->create($rawWalletData['balance'], $walletCurrency->currencyCode),
                $walletCurrency
            );

            foreach ($transactions as $transaction) {
                $wallet->registerTransaction($transaction);
            }

            $wallets[] = $wallet;
        }

        return $wallets;
    }

    public function fetchByIdAndOwnerExternalId(WalletId $walletId, WalletOwnerExternalId $ownerId): Wallet
    {
        $wallet = $this->fetchById($walletId);

        foreach ($wallet->getOwners() as $walletOwner) {
            if ($walletOwner->externalId->toString() === $ownerId->toString()) {
                return $wallet;
            }
        }

        throw WalletDoesNotExist::withId($walletId->toString());
    }

    public function fetchWalletCurrency(WalletId $walletId): WalletCurrency
    {
        /** @var array{currency_code: string, currency_id: string}|false $rawData */
        $rawData = $this->connection->createQueryBuilder()
            ->select('currency_code', 'currency_id')
            ->from(self::DB_TABLE_NAME)
            ->where('id = :id')
            ->setParameter('id', $walletId->toString())
            ->fetchAssociative();

        if (!$rawData) {
            throw WalletDoesNotExist::withId($walletId->toString());
        }

        return new WalletCurrency(
            WalletCurrencyId::fromString($rawData['currency_id']),
            $rawData['currency_code']
        );
    }

    public function doesWalletBelongToOwner(WalletId $walletId, WalletOwnerExternalId $ownerId): bool
    {
        /** @var array{count: int}|false $rawData */
        $rawData = $this->connection->createQueryBuilder()
            ->select('count(id) as count')
            ->from(self::WALLET_OWNERS_DB_TABLE_NAME)
            ->where('external_id = :ownerExternalId')
            ->andWhere('wallet_id = :walletId')
            ->setParameters([
                'ownerExternalId' => $ownerId->toString(),
                'walletId' => $walletId->toString(),
            ])
            ->fetchAssociative();

        if (!$rawData) {
            return false;
        }

        return $rawData['count'] > 0;
    }
}
