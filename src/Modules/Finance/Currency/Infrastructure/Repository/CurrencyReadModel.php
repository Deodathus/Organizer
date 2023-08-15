<?php

declare(strict_types=1);

namespace App\Modules\Finance\Currency\Infrastructure\Repository;

use App\Modules\Finance\Currency\Application\Exception\CurrencyDoesNotExistException;
use App\Modules\Finance\Currency\Application\ReadModel\CurrencyReadModel as CurrencyReadModelInterface;
use App\Modules\Finance\Currency\Application\ViewModel\CurrencyViewModel;
use App\Modules\Finance\Currency\Domain\ValueObject\CurrencyId;
use Doctrine\DBAL\Connection;

final class CurrencyReadModel implements CurrencyReadModelInterface
{
    private const TABLE_NAME = 'currencies';

    public function __construct(
        private readonly Connection $connection
    ) {
    }

    public function fetch(CurrencyId $id): CurrencyViewModel
    {
        $rawData = $this->connection
            ->createQueryBuilder()
            ->select('id', 'code')
            ->from(self::TABLE_NAME, 'c')
            ->where('c.id = :id')
            ->setParameter('id', $id->toString())
            ->fetchAssociative();

        if (!$rawData) {
            throw CurrencyDoesNotExistException::withId($id->toString());
        }

        return new CurrencyViewModel($rawData['id'], strtoupper($rawData['code']));
    }
}
