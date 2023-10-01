<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Infrastructure\Http\Request;

use App\SharedInfrastructure\Http\Headers;
use App\SharedInfrastructure\Http\Request\AbstractRequest;
use Assert\Assert;
use Symfony\Component\HttpFoundation\Request as ServerRequest;

final class StoreTransactionRequest extends AbstractRequest
{
    private function __construct(
        public string $walletId,
        public string $transactionCreatorApiToken,
        public string $transactionAmount,
        public string $transactionCurrencyCode,
        public string $transactionType,
        public ?string $receiverWalletId = null
    ) {
    }

    public static function fromRequest(ServerRequest $request): AbstractRequest
    {
        $requestStack = $request->toArray();

        /** @var string $walletId */
        $walletId = $request->get('walletId') ?? '';
        $transactionCreatorApiToken = $request->headers->get(Headers::AUTH_TOKEN_HEADER->value) ?? '';
        $transactionAmount = $requestStack['transactionAmount'] ?? null;
        $transactionCurrencyCode = $requestStack['transactionCurrencyCode'] ?? null;
        $transactionType = $requestStack['transactionType'] ?? null;
        $receiverWalletId = $requestStack['receiverWalletId'] ?? null;

        Assert::lazy()
            ->that($walletId, 'walletId')->string()->notEmpty()->uuid()
            ->that($transactionCreatorApiToken, 'transactionCreatorApiToken')->string()->notEmpty()->notNull()
            ->that($transactionAmount, 'transactionAmount')->string()->numeric()->greaterThan(0)
            ->that($transactionCurrencyCode, 'transactionCurrencyCode')->string()->notEmpty()->notNull()
            ->that($transactionType, 'transactionType')->string()->notEmpty()->notNull()
            ->that($receiverWalletId, 'receiverWalletId')->nullOr()->string()->uuid()
            ->verifyNow();

        return new self(
            $walletId,
            $transactionCreatorApiToken,
            $transactionAmount,
            $transactionCurrencyCode,
            $transactionType,
            $receiverWalletId
        );
    }

    /**
     * @return array{walletId: string, transactionCreatorApiToken: string, transactionAmount: string, transactionCurrencyCode: string, transactionType: string, receiverWalletId: null|string}
     */
    public function toArray(): array
    {
        return [
            'walletId' => $this->walletId,
            'transactionCreatorApiToken' => $this->transactionCreatorApiToken,
            'transactionAmount' => $this->transactionAmount,
            'transactionCurrencyCode' => $this->transactionCurrencyCode,
            'transactionType' => $this->transactionType,
            'receiverWalletId' => $this->receiverWalletId,
        ];
    }
}
