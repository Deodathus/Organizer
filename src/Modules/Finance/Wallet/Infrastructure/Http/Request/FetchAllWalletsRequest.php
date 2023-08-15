<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Infrastructure\Http\Request;

use App\SharedInfrastructure\Http\Headers;
use App\SharedInfrastructure\Http\Request\AbstractRequest;
use Assert\Assert;
use Symfony\Component\HttpFoundation\Request as ServerRequest;

final class FetchAllWalletsRequest extends AbstractRequest
{
    private function __construct(
        public readonly string $requesterToken
    ) {}

    public static function fromRequest(ServerRequest $request): AbstractRequest
    {
        $requesterToken = $request->headers->get(Headers::AUTH_TOKEN_HEADER->value);

        Assert::lazy()
            ->that($requesterToken, 'requesterToken')->string()->notBlank()
            ->verifyNow();

        return new self($requesterToken);
    }

    public function toArray(): array
    {
        return [
            'requesterToken' => $this->requesterToken,
        ];
    }
}
