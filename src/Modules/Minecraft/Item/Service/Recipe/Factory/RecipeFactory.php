<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Recipe\Factory;

use App\Modules\Minecraft\Item\DTO\Recipe\IngredientDTO;
use App\Modules\Minecraft\Item\DTO\Recipe\RecipeResultDTO;
use App\Modules\Minecraft\Item\DTO\Recipe\StoreRecipeDTO;
use App\Modules\Minecraft\Item\Entity\Ingredient;
use App\Modules\Minecraft\Item\Entity\Item;
use App\Modules\Minecraft\Item\Entity\Recipe;
use App\Modules\Minecraft\Item\Entity\RecipeResult;
use App\Modules\Minecraft\Item\Exception\RecipeFactoryBuildException;
use Doctrine\Common\Collections\ArrayCollection;

final class RecipeFactory implements RecipeFactoryInterface
{
    /**
     * @throws RecipeFactoryBuildException
     */
    public function build(StoreRecipeDTO $recipeDTO, array $usedItems): Recipe
    {
        $ingredients = $this->buildIngredients($recipeDTO->getIngredients(), $usedItems);
        $results = $this->buildResults($recipeDTO->getResults(), $usedItems);

        $recipe = new Recipe(
            name: $recipeDTO->getName(),
            ingredients: new ArrayCollection($ingredients),
            results: new ArrayCollection($results)
        );

        foreach ($ingredients as $ingredient) {
            $ingredient->addRecipeUsedIn($recipe);
        }

        foreach ($results as $result) {
            $result->updateRecipe($recipe);
        }

        return $recipe;
    }

    /**
     * @param IngredientDTO[] $ingredients
     * @param Item[] $usedItems
     *
     * @return Ingredient[]
     *
     * @throws RecipeFactoryBuildException
     */
    private function buildIngredients(array $ingredients, array $usedItems): array
    {
        $result = [];

        foreach ($ingredients as $ingredient) {
            $item = $usedItems[$ingredient->getItemId()] ?? null;

            if ($item) {
                $result[] = new Ingredient($ingredient->getAmount(), $item);

                continue;
            }

            throw new RecipeFactoryBuildException(
                sprintf(
                    '[RecipeFactory] Cannot fetch item for ingredient; Item ID: %d',
                    $ingredient->getItemId()
                )
            );
        }

        return $result;
    }

    /**
     * @param RecipeResultDTO[] $results
     * @param Item[] $usedItems
     *
     * @return RecipeResult[]
     *
     * @throws RecipeFactoryBuildException
     */
    private function buildResults(array $results, array $usedItems): array
    {
        $builtResults = [];

        foreach ($results as $result) {
            $item = $usedItems[$result->getItemId()] ?? null;

            if ($item) {
                $builtResults[] = new RecipeResult($result->getAmount(), $item);

                continue;
            }

            throw new RecipeFactoryBuildException(
                sprintf(
                    '[RecipeFactory] Cannot fetch item for recipe result; Item ID: %d',
                    $result->getItemId()
                )
            );
        }

        return $builtResults;
    }
}
