<?php
declare(strict_types=1);

namespace App\Modules\Authentication\Infrastructure\Http\Request;

use App\SharedInfrastructure\Http\Headers;
use App\SharedInfrastructure\Http\Request\AbstractRequest;
use Assert\Assert;
use Symfony\Component\HttpFoundation\Request as ServerRequest;

final class GetMeRequest extends AbstractRequest
{
    private function __construct(
        public readonly string $token
    ) {}

    public static function fromRequest(ServerRequest $request): AbstractRequest
    {
        $token = $request->headers->get(Headers::AUTH_TOKEN_HEADER->value);

        if ($token === null) {
            $token = $request->get(Headers::AUTH_TOKEN_HEADER->value);
        }

        Assert::lazy()
            ->that($token, 'token')->string()->notEmpty()->maxLength(255)
            ->verifyNow();

        return new self($token);
    }

    public function toArray(): array
    {
        return [
            'token' => $this->token,
        ];
    }
}
