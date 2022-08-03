<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Request\Item;

use App\Request\AbstractRequest;
use Assert\Assert;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\Request as ServerRequest;

class FetchItemRecipesRequest extends AbstractRequest
{
    private function __construct(
        public readonly int $perPage,
        public readonly int $page,
        public readonly int $itemId
    ) {}

    public static function fromRequest(ServerRequest $request): AbstractRequest
    {
        $perPage = (int) ($request->query->get('perPage') ?? 60);
        $page = (int) ($request->query->get('page') ?? 1);
        $itemId = (int) $request->get('id');

        Assert::lazy()
            ->that($perPage, 'perPage')->integer()->min(1)->max(500)
            ->that($page, 'page')->integer()->min(1)
            ->that($itemId, 'itemId')->integer()->min(1)
            ->verifyNow();

        return new self($perPage, $page, $itemId);
    }

    #[ArrayShape(['perPage' => "int", 'page' => "int", 'itemId' => "int"])]
    public function toArray(): array
    {
        return [
            'perPage' => $this->perPage,
            'page' => $this->page,
            'itemId' => $this->itemId,
        ];
    }
}
