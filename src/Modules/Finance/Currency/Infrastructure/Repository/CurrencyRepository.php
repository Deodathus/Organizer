<?php
declare(strict_types=1);

namespace App\Modules\Finance\Currency\Infrastructure\Repository;

use App\Modules\Finance\Currency\Domain\Entity\Currency;
use App\Modules\Finance\Currency\Domain\Exception\CurrencyWIthGivenCodeAlreadyExists;
use App\Modules\Finance\Currency\Domain\Repository\CurrencyRepository as CurrencyRepositoryInterface;
use App\Modules\Finance\Currency\Domain\ValueObject\CurrencyCode;
use App\Modules\Finance\Currency\Domain\ValueObject\CurrencyId;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

final class CurrencyRepository implements CurrencyRepositoryInterface
{
    private const TABLE_NAME = 'currencies';

    public function __construct(
        private readonly Connection $connection
    ) {}

    public function store(Currency $currency): void
    {
        try {
            $this->connection
                ->createQueryBuilder()
                ->insert(self::TABLE_NAME)
                ->values([
                    'id' => ':id',
                    'code' => ':code',
                ])
                ->setParameters([
                    'id' => $currency->getId()->toString(),
                    'code' => $currency->getCode()->value,
                ])
                ->executeStatement();
        } catch (UniqueConstraintViolationException $exception) {
            throw CurrencyWIthGivenCodeAlreadyExists::withCode($currency->getCode()->value);
        }
    }

    public function delete(CurrencyId $currencyId): void
    {
        $this->connection
            ->createQueryBuilder()
            ->delete(self::TABLE_NAME)
            ->where('id = :id')
            ->setParameter('id', $currencyId->toString())
            ->executeStatement();
    }

    public function fetchByCode(CurrencyCode $code): Currency
    {
        $rawResult = $this->connection->createQueryBuilder();
    }
}
