<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Request\Item;

use App\Request\AbstractRequest;
use Assert\Assert;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\Request as ServerRequest;

final class ItemStoreRequest extends AbstractRequest
{
    private function __construct(
        public readonly int $key,
        public readonly string $name,
        public readonly ?int $subKey
    ) {
    }

    public static function fromRequest(ServerRequest $request): self
    {
        $requestStack = $request->toArray();

        $key = $requestStack['key'] ?? null;
        $name = $requestStack['name'] ?? null;
        $subKey = $requestStack['subKey'] ?? null;

        Assert::lazy()
            ->that($name, 'name')->notEmpty()->maxLength(254)
            ->that($key, 'key')->integer()
            ->that($subKey, 'subKey')->nullOr()->integer()
            ->verifyNow();

        return new self($key, $name, $subKey);
    }

    #[ArrayShape(['key' => 'int', 'name' => 'string', 'subKey' => 'int|null'])]
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'name' => $this->name,
            'subKey' => $this->subKey,
        ];
    }
}
