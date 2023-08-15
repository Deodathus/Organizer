<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Request\Recipe;

use App\Request\AbstractRequest;
use Assert\Assert;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\Request as ServerRequest;

final class RecipeCalculateRequest extends AbstractRequest
{
    public function __construct(private readonly int $recipeId, private readonly int $amount)
    {
    }

    public static function fromRequest(ServerRequest $request): AbstractRequest
    {
        $recipeId = (int) $request->attributes->get('recipeId');
        $amount = (int) $request->query->get('amount');

        Assert::lazy()
            ->that($recipeId, 'recipeId')->notNull()->integer()->greaterThan(0)
            ->that($amount)->notNull()->integer()->greaterThan(0)
            ->verifyNow();

        return new self(recipeId: $recipeId, amount: $amount);
    }

    public function getRecipeId(): int
    {
        return $this->recipeId;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    #[ArrayShape(['recipeId' => 'int', 'amount' => 'int'])]
    public function toArray(): array
    {
        return [
            'recipeId' => $this->recipeId,
            'amount' => $this->amount,
        ];
    }
}
