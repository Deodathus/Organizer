<?php
declare(strict_types=1);

namespace App\Modules\Finance\Currency\Infrastructure\Http\Request;

use App\SharedInfrastructure\Http\Request\AbstractRequest;
use Assert\Assert;
use Symfony\Component\HttpFoundation\Request as ServerRequest;

final class DeleteCurrencyRequest extends AbstractRequest
{
    private function __construct(
        public readonly string $id
    ) {}

    public static function fromRequest(ServerRequest $request): AbstractRequest
    {
        $currencyId = $request->get('id');

        Assert::lazy()
            ->that($currencyId, 'currencyId')->string()->uuid()->notNull()
            ->verifyNow();

        return new self($currencyId);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
        ];
    }
}
