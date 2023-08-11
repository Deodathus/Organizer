<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Infrastructure\Http\Request;

use App\SharedInfrastructure\Http\Headers;
use App\SharedInfrastructure\Http\Request\AbstractRequest;
use Assert\Assert;
use Symfony\Component\HttpFoundation\Request as ServerRequest;

final class StoreExpenseCategoryRequest extends AbstractRequest
{
    private function __construct(
        public string $name,
        public string $creatorToken
    ) {}

    public static function fromRequest(ServerRequest $request): AbstractRequest
    {
        $name = $request->toArray()['name'] ?? null;
        $creatorToken = $request->headers->get(Headers::AUTH_TOKEN_HEADER->value);

        Assert::lazy()
            ->that($name, 'name')->string()->notEmpty()
            ->that($creatorToken, 'creatorToken')->string()->notEmpty()
            ->verifyNow();

        return new self($name, $creatorToken);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'creatorToken' => $this->creatorToken,
        ];
    }
}
