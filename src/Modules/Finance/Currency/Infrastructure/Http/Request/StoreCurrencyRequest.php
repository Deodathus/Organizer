<?php

declare(strict_types=1);

namespace App\Modules\Finance\Currency\Infrastructure\Http\Request;

use App\SharedInfrastructure\Http\Request\AbstractRequest;
use Assert\Assert;
use Symfony\Component\HttpFoundation\Request as ServerRequest;

final class StoreCurrencyRequest extends AbstractRequest
{
    private function __construct(
        public readonly string $code
    ) {
    }

    public static function fromRequest(ServerRequest $request): AbstractRequest
    {
        $requestStack = $request->toArray();

        $currencyCode = $requestStack['code'] ?? null;

        Assert::lazy()
            ->that($currencyCode, 'currencyCode')->string()->notNull()
            ->verifyNow();

        return new self($currencyCode);
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
        ];
    }
}
