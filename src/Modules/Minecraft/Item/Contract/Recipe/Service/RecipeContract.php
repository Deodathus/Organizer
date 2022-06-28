<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Contract\Recipe\Service;

use App\Modules\Minecraft\Item\Contract\Recipe\Exception\RecipeNotFound;
use App\Modules\Minecraft\Item\Entity\Recipe;
use App\Modules\Minecraft\Item\Exception\RecipeDoesNotExist;
use App\Modules\Minecraft\Item\Service\Recipe\RecipeServiceInterface;

final class RecipeContract implements RecipeContractInterface
{
    public function __construct(private readonly RecipeServiceInterface $recipeService) {}

    /**
     * @throws RecipeNotFound
     */
    public function fetch(int $recipeId): Recipe
    {
        try {
            return $this->recipeService->fetch($recipeId);
        } catch (RecipeDoesNotExist $exception) {
            throw RecipeNotFound::fromException($exception);
        }
    }
}
