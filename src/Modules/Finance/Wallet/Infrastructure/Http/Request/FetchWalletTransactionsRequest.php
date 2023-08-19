<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Infrastructure\Http\Request;

use App\SharedInfrastructure\Http\Headers;
use App\SharedInfrastructure\Http\Request\AbstractRequest;
use Assert\Assert;
use Symfony\Component\HttpFoundation\Request as ServerRequest;

final class FetchWalletTransactionsRequest extends AbstractRequest
{
    private function __construct(
        public readonly string $walletId,
        public readonly string $requesterToken,
        public readonly int $perPage,
        public readonly int $page
    ) {
    }

    public static function fromRequest(ServerRequest $request): AbstractRequest
    {
        /** @var string $walletId */
        $walletId = $request->get('walletId');
        /** @var string $requesterToken */
        $requesterToken = $request->headers->get(Headers::AUTH_TOKEN_HEADER->value);
        /** @var string $perPage */
        $perPage = $request->get('perPage', 40);
        /** @var string $page */
        $page = $request->get('page', 1);

        Assert::lazy()
            ->that($walletId, 'walletId')->uuid()->notBlank()
            ->that($requesterToken, 'requesterToken')->string()->notBlank()
            ->that($perPage, 'perPage')->numeric()->greaterThan(0)
            ->that($page, 'page')->numeric()->greaterThan(0)
            ->verifyNow();

        return new self($walletId, $requesterToken, (int) $perPage, (int) $page);
    }

    /**
     * @return array{walletId: string, requesterToken: string, perPage: int, page: int}
     */
    public function toArray(): array
    {
        return [
            'walletId' => $this->walletId,
            'requesterToken' => $this->requesterToken,
            'perPage' => $this->perPage,
            'page' => $this->page,
        ];
    }
}
