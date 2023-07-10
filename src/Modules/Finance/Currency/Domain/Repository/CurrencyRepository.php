<?php
declare(strict_types=1);

namespace App\Modules\Finance\Currency\Domain\Repository;

use App\Modules\Finance\Currency\Domain\Entity\Currency;
use App\Modules\Finance\Currency\Domain\ValueObject\CurrencyId;

interface CurrencyRepository
{
    public function store(Currency $currency): void;

    public function delete(CurrencyId $currencyId): void;
}
