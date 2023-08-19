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
        public readonly ?int $subKey,
        public readonly ?string $itemTag
    ) {
    }

    public static function fromRequest(ServerRequest $request): self
    {
        $requestStack = $request->toArray();

        $key = $requestStack['key'] ?? null;
        $name = $requestStack['name'] ?? null;
        $subKey = $requestStack['subKey'] ?? null;
        $itemTag = $requestStack['itemTag'] ?? '';

        Assert::lazy()
            ->that($name, 'name')->string()->notEmpty()->maxLength(255)
            ->that($itemTag, 'itemTag')->string()->maxLength(255)
            ->that($key, 'key')->integer()
            ->that($subKey, 'subKey')->nullOr()->integer()
            ->verifyNow();

        return new self($key, $name, $subKey, $itemTag);
    }

    #[ArrayShape(['key' => 'int', 'name' => 'string', 'itemTag' => 'string', 'subKey' => 'int|null'])]
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'name' => $this->name,
            'itemTag' => $this->itemTag,
            'subKey' => $this->subKey,
        ];
    }
}
