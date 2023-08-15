<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Infrastructure\Http\Request;

use App\SharedInfrastructure\Http\Headers;
use App\SharedInfrastructure\Http\Request\AbstractRequest;
use Assert\Assert;
use Symfony\Component\HttpFoundation\Request as ServerRequest;

final class FetchWalletRequest extends AbstractRequest
{
    private function __construct(
        public readonly string $requesterToken,
        public readonly string $walletId
    ) {
    }

    public static function fromRequest(ServerRequest $request): AbstractRequest
    {
        /** @var string $walletId */
        $walletId = $request->get('walletId');
        /** @var string $requesterToken */
        $requesterToken = $request->headers->get(Headers::AUTH_TOKEN_HEADER->value);

        Assert::lazy()
            ->that($walletId, 'walletId')->string()->uuid()->notBlank()
            ->that($requesterToken, 'requesterToken')->string()->notBlank()
            ->verifyNow();

        return new self($requesterToken, $walletId);
    }

    /**
     * @return array{requesterToken: string, walletId: string}
     */
    public function toArray(): array
    {
        return [
            'requesterToken' => $this->requesterToken,
            'walletId' => $this->walletId,
        ];
    }
}
