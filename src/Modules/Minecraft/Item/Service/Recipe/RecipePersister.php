<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Recipe;

use App\Modules\Minecraft\Item\DTO\Recipe\StoreRecipeDTO;
use App\Modules\Minecraft\Item\Exception\CannotFetchItemsException;
use App\Modules\Minecraft\Item\Exception\RecipeStoreException;
use App\Modules\Minecraft\Item\Repository\RecipeRepository;
use App\Modules\Minecraft\Item\Service\Item\ItemFetcher;
use App\Modules\Minecraft\Item\Service\Recipe\Factory\RecipeFactoryInterface;

final class RecipePersister
{
    public function __construct(
        private readonly ItemFetcher $itemFetcher,
        private readonly RecipeRepository $recipeRepository,
        private readonly RecipeFactoryInterface $recipeFactory
    ) {
    }

    /**
     * @throws RecipeStoreException
     */
    public function store(StoreRecipeDTO $recipeDTO): int
    {
        try {
            $items = $this->itemFetcher->fetchByIds($recipeDTO->getItemsInRecipeIds());
        } catch (CannotFetchItemsException $exception) {
            throw RecipeStoreException::fromException($exception);
        }

        $recipe = $this->recipeFactory->build($recipeDTO, $items);

        $this->recipeRepository->store($recipe);
        $this->recipeRepository->flush();

        return $recipe->getId();
    }
}
