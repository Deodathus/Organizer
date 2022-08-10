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
<<<<<<< HEAD
        public readonly ?int $subKey
    ) {
    }
=======
        public readonly ?int $subKey,
        public readonly ?string $itemTag
    ){}
>>>>>>> Added item types.

    public static function fromRequest(ServerRequest $request): self
    {
        $requestStack = $request->toArray();

        $key = $requestStack['key'] ?? null;
        $name = $requestStack['name'] ?? null;
        $subKey = $requestStack['subKey'] ?? null;
        $itemTag = $requestStack['subKey'] ?? '';

        Assert::lazy()
            ->that($name, 'name')->string()->notEmpty()->maxLength(255)
            ->that($itemTag, 'itemTag')->string()->maxLength(255)
            ->that($key, 'key')->integer()
            ->that($subKey, 'subKey')->nullOr()->integer()
            ->verifyNow();

        return new self($key, $name, $subKey, $itemTag);
    }

<<<<<<< HEAD
    #[ArrayShape(['key' => 'int', 'name' => 'string', 'subKey' => 'int|null'])]
=======
    #[ArrayShape(['key' => "int", 'name' => "string", 'itemTag' => 'string', 'subKey' => "int|null"])]
>>>>>>> Added item types.
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
