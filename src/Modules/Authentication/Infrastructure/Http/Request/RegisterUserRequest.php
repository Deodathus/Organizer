<?php
declare(strict_types=1);

namespace App\Modules\Authentication\Infrastructure\Http\Request;

use App\SharedInfrastructure\Http\Request\AbstractRequest;
use Assert\Assert;
use Symfony\Component\HttpFoundation\Request as ServerRequest;

final class RegisterUserRequest extends AbstractRequest
{
    private function __construct(
        public readonly string $userId
    ) {}

    public static function fromRequest(ServerRequest $request): AbstractRequest
    {
        $userId = $request->toArray()['userId'];
        Assert::lazy()
            ->that($userId, 'userId')->string()->uuid()->maxLength(225)
            ->verifyNow();

        return new self($userId);
    }

    public function toArray(): array
    {
        return [
            'userId' => $this->userId,
        ];
    }
}
