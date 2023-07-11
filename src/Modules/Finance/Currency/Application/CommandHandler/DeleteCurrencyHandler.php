<?php
declare(strict_types=1);

namespace App\Modules\Finance\Currency\Application\CommandHandler;

use App\Modules\Finance\Currency\Application\Command\DeleteCurrency;
use App\Modules\Finance\Currency\Domain\Repository\CurrencyRepository;
use App\Modules\Finance\Currency\Domain\ValueObject\CurrencyId;
use App\Shared\Application\Messenger\CommandHandler;

final class DeleteCurrencyHandler implements CommandHandler
{
    public function __construct(
        private readonly CurrencyRepository $currencyRepository
    ) {}

    public function __invoke(DeleteCurrency $deleteCurrency): void
    {
        $this->currencyRepository->delete(
            CurrencyId::fromString($deleteCurrency->currencyId)
        );
    }
}
