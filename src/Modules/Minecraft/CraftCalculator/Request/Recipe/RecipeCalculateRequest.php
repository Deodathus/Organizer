<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\CraftCalculator\Request\Recipe;

use App\Request\AbstractRequest;
use Assert\Assert;
use Symfony\Component\HttpFoundation\Request as ServerRequest;

final class RecipeCalculateRequest extends AbstractRequest
{
    public function __construct(private int $recipeId, private int $amount) {}

    public static function fromRequest(ServerRequest $request): AbstractRequest
    {
        $requestStack = $request->toArray();

        $recipeId = (int) $request->attributes->get('recipeId');
        $amount = $requestStack['amount'] ?? null;

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
}
