<?php

namespace App\Modules\Finance\Expense\Infrastructure\Http\Request;

use App\SharedInfrastructure\Http\Headers;
use App\SharedInfrastructure\Http\Request\AbstractRequest;
use Assert\Assert;
use Symfony\Component\HttpFoundation\Request as ServerRequest;

final class FetchMonthlyExpenseRequest extends AbstractRequest
{
    private function __construct(
        public readonly string $requestToken,
        public readonly int $month,
    ) {}

    public static function fromRequest(ServerRequest $request): AbstractRequest
    {
        /** @var string $requesterToken */
        $requesterToken = $request->headers->get(Headers::AUTH_TOKEN_HEADER->value);
        /** @var string $month */
        $month = $request->get('month');

        Assert::lazy()
            ->that($requesterToken, 'requesterToken')->string()->notBlank()
            ->that($month, 'month')->greaterThan(0)->lessOrEqualThan(12)->notBlank()
            ->verifyNow();

        return new self($requesterToken, $month);
    }

    public function toArray(): array
    {
        return [
            'requestToken' => $this->requestToken,
            'month' => $this->month,
        ];
    }
}