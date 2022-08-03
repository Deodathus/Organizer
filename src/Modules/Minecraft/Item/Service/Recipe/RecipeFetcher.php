<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Recipe;

use App\Modules\Minecraft\Item\Entity\Recipe;
use App\Modules\Minecraft\Item\Exception\RecipeDoesNotExist;
use App\Modules\Minecraft\Item\Repository\RecipeRepository;
use App\Modules\Minecraft\Item\Search\FilterBus;
use App\Modules\Minecraft\Item\Search\PaginatedResult;

final class RecipeFetcher
{
    public function __construct(
        private readonly RecipeRepository $recipeRepository
    ) {}

    /**
     * @throws RecipeDoesNotExist
     */
    public function fetch(int $id): Recipe
    {
        if ($recipe = $this->recipeRepository->fetch($id)) {
            return $recipe;
        }

        throw new RecipeDoesNotExist(sprintf('Recipe ID: %d', $id));
    }

    public function fetchByItemIngredientId(int $id, FilterBus $filterBus): PaginatedResult
    {
        return $this->recipeRepository->fetchByItemIngredientIdPaginated($id, $filterBus);
    }

    public function fetchByItemResultId(int $id, FilterBus $filterBus): PaginatedResult
    {
        return $this->recipeRepository->fetchByItemResultIdPaginated($id, $filterBus);
    }
}
