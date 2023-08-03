<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Infrastructure\Http\Request;

use App\SharedInfrastructure\Http\Headers;
use App\SharedInfrastructure\Http\Request\AbstractRequest;
use Assert\Assert;
use Symfony\Component\HttpFoundation\Request as ServerRequest;

final class StoreWalletRequest extends AbstractRequest
{
    private function __construct(
        public readonly string $name,
        public readonly int $balance,
        public readonly string $currencyCode,
        public readonly string $creatorToken
    ) {}

    public static function fromRequest(ServerRequest $request): AbstractRequest
    {
        $requestStack = $request->toArray();

        $name = $requestStack['name'] ?? null;
        $balance = $requestStack['balance'] ?? null;
        $currencyCode = $requestStack['currencyCode'] ?? null;
        $creatorToken = $request->headers->get(Headers::AUTH_TOKEN_HEADER->value);

        Assert::lazy()
            ->that($name, 'name')->string()->notEmpty()
            ->that($balance, 'balance')->numeric()->min(0)
            ->that($currencyCode, 'currencyCode')->string()
            ->that($creatorToken, 'creatorToken')->string()->notEmpty()
            ->verifyNow();

        return new self(
            $name,
            $balance,
            $currencyCode,
            $creatorToken
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'balance' => $this->balance,
            'currencyId' => $this->currencyCode,
            'creatorToken' => $this->creatorToken,
        ];
    }
}
