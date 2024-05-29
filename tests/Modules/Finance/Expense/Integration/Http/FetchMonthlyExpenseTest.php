<?php

namespace App\Tests\Modules\Finance\Expense\Integration\Http;

use App\Tests\SharedInfrastructure\IntegrationTestBase;

/** @group integration */
final class FetchMonthlyExpenseTest extends IntegrationTestBase
{
    private const ENDPOINT_URL = '/api/finance/expense/monthly';
    private const EXPENSE_AMOUNT = '31.45';
    private const EXPENSE_CURRENCY_CODE = 'CAD';
}