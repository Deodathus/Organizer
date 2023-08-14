<?php

declare(strict_types=1);

namespace App\Tests\Modules\Finance\Expense\Integration\TestUtils;

use App\Modules\Finance\Currency\Domain\Entity\Currency;
use App\Modules\Finance\Currency\Domain\Repository\CurrencyRepository;
use App\Modules\Finance\Currency\Domain\ValueObject\CurrencyCode;
use App\Modules\Finance\Currency\ModuleAPI\Application\Enum\SupportedCurrencies;
use App\Modules\Finance\Expense\Domain\Entity\Expense;
use App\Modules\Finance\Expense\Domain\Entity\ExpenseCategory;
use App\Modules\Finance\Expense\Domain\Repository\ExpenseCategoryRepository;
use App\Modules\Finance\Expense\Domain\Repository\ExpenseRepository;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseCategoryId;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseCategoryOwnerId;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseId;
use App\Modules\Finance\Wallet\Domain\Entity\Wallet;
use App\Modules\Finance\Wallet\Domain\Entity\WalletOwner;
use App\Modules\Finance\Wallet\Domain\Repository\WalletRepository;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletCurrency;
use App\Tests\Modules\Finance\Expense\TestUtils\ExpenseCategoryMother;
use App\Tests\Modules\Finance\Wallet\TestUtils\Mother\WalletMother;

final readonly class ExpenseService
{
    public function __construct(
        private CurrencyRepository $currencyRepository,
        private ExpenseCategoryRepository $expenseCategoryRepository,
        private WalletRepository $walletRepository,
        private ExpenseRepository $expenseRepository
    ) {}

    /**
     * @param WalletOwner[] $owners
     */
    public function storeWallet(
        array $owners,
        WalletCurrency $walletCurrency,
    ): Wallet {
        $wallet = WalletMother::create($owners, $walletCurrency);

        $this->walletRepository->store($wallet);

        return $wallet;
    }

    public function storeCurrency(SupportedCurrencies $code): Currency
    {
        $currency = Currency::create(CurrencyCode::from($code->value));

        $this->currencyRepository->store($currency);

        return $currency;
    }

    public function storeExpenseCategory(ExpenseCategoryOwnerId $ownerId): ExpenseCategory
    {
        $category = ExpenseCategoryMother::create($ownerId);

        $this->expenseCategoryRepository->store($category);

        return $category;
    }

    public function fetchCategoryById(ExpenseCategoryId $categoryId): ExpenseCategory
    {
        return $this->expenseCategoryRepository->fetchById($categoryId);
    }

    public function fetchExpenseById(ExpenseId $expenseId): Expense
    {
        return $this->expenseRepository->fetchById($expenseId);
    }
}
