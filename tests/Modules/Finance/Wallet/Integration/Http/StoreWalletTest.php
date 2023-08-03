<?php

declare(strict_types=1);

namespace App\Tests\Modules\Finance\Wallet\Integration\Http;

use App\Tests\Modules\Finance\Wallet\Integration\TestUtils\WalletService;
use App\Tests\SharedInfrastructure\IntegrationTestBase;

final class StoreWalletTest extends IntegrationTestBase
{
    private const API_URL = '/api/finance/currency';
    private const PLN_CURRENCY_CODE = 'PLN';
    private const UNKNOWN_CURRENCY_CODE = 'xxx';
    private WalletService $walletService;

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpAuthUserProvider();

        $this->walletService = $this->container->get(WalletService::class);
    }

    public function shouldNotStoreWalletWithUnauthorizedUser(): void
    {

    }
}
