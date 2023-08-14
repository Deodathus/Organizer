<?php
declare(strict_types=1);

namespace App\Modules\Finance\Expense\Infrastructure\Http\Request;

use App\SharedInfrastructure\Http\Headers;
use App\SharedInfrastructure\Http\Request\AbstractRequest;
use Assert\Assert;
use Symfony\Component\HttpFoundation\Request as ServerRequest;

final class StoreExpenseRequest extends AbstractRequest
{
    private function __construct(
        public readonly string $walletId,
        public readonly string $ownerApiToken,
        public readonly string $categoryId,
        public readonly string $amount,
        public readonly string $currencyCode,
        public readonly ?string $comment = null
    ) {}

    public static function fromRequest(ServerRequest $request): AbstractRequest
    {
        $requestStack = $request->toArray();

        $walletId = $requestStack['walletId'] ?? null;
        $ownerApiToken = $request->headers->get(Headers::AUTH_TOKEN_HEADER->value);
        $categoryId = $requestStack['categoryId'] ?? null;
        $amount = $requestStack['amount'] ?? null;
        $currencyCode = $requestStack['currencyCode'] ?? null;
        $comment = $requestStack['comment'] ?? null;

        Assert::lazy()
            ->that($walletId,'walletId')->string()->uuid()->notBlank()->notNull()
            ->that($ownerApiToken, 'ownerApiToken')->string()->notBlank()->notNull()
            ->that($categoryId, 'categoryId')->string()->uuid()->notBlank()->notNull()
            ->that($amount, 'amount')->string()->numeric()->greaterThan(0)
            ->that($currencyCode, 'currencyCode')->string()->notBlank()->notNull()
            ->that($comment, 'comment')->nullOr()->string()
            ->verifyNow();

        return new self(
            $walletId,
            $ownerApiToken,
            $categoryId,
            $amount,
            $currencyCode,
            $comment
        );
    }

    public function toArray(): array
    {
        return [
            'walletId' => $this->walletId,
            'ownerApiToken' => $this->ownerApiToken,
            'categoryId' => $this->categoryId,
            'amount' => $this->amount,
            'currencyCode' => $this->currencyCode,
            'comment' => $this->comment,
        ];
    }
}
