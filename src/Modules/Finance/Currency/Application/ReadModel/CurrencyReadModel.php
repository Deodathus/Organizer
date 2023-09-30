<?php

declare(strict_types=1);

namespace App\Modules\Finance\Currency\Application\ReadModel;

use App\Modules\Finance\Currency\Application\ViewModel\CurrencyViewModel;
use App\Modules\Finance\Currency\Domain\ValueObject\CurrencyId;

interface CurrencyReadModel
{
    public function fetch(CurrencyId $id): CurrencyViewModel;

    /**
     * @return CurrencyViewModel[]
     */
    public function fetchAll(): array;
}
