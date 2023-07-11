<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Infrastructure\Repository;

use App\Modules\Finance\Wallet\Domain\Entity\Wallet;
use App\Modules\Finance\Wallet\Domain\Repository\WalletRepository as WalletRepositoryInterface;
use Doctrine\DBAL\Connection;

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
                'currency_id' => ':currency_id',
            ])
            ->setParameters([
                'id' => $wallet->getId()->toString(),
                'name' => $wallet->getName(),
                'balance' => $wallet->getBalance()->toString(),
                'currency_id' => $wallet->getCurrencyId()->toString(),
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
}
