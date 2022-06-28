<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Transformer;

use App\Modules\Minecraft\Item\Entity\Item;
use App\Modules\Minecraft\Item\Entity\Recipe;
use App\Modules\Minecraft\Item\Response\Model\IngredientModel;
use App\Modules\Minecraft\Item\Response\Model\ItemRecipesModel;
use App\Modules\Minecraft\Item\Response\Model\RecipeModel;
use App\Modules\Minecraft\Item\Response\Model\RecipeResultModel;

final class ItemToItemRecipesModelTransformer implements ItemToItemRecipesModelTransformerInterface
{
    public function transform(Item $item): ItemRecipesModel
    {
        $asIngredient = [];
        $asResult = [];

        foreach ($item->getAsIngredients() as $ingredient) {
            foreach ($ingredient->getUsedInRecipes() as $recipeWithAsIngredient) {
                $asIngredient[] = $this->buildRecipeModel($recipeWithAsIngredient);
            }
        }

        foreach ($item->getRecipeResult() as $recipeWithAsResult) {
            $asResult[] = $this->buildRecipeModel($recipeWithAsResult->getRecipe());
        }

        return new ItemRecipesModel(
            itemId: $item->getId(),
            asIngredient: $asIngredient,
            asResult: $asResult
        );
    }

    private function buildRecipeModel(Recipe $recipe): RecipeModel
    {
        return new RecipeModel(
            id: $recipe->getId(),
            name: $recipe->getName(),
            ingredients: $this->buildIngredients($recipe),
            results: $this->buildResults($recipe)
        );
    }

    /**
     * @return IngredientModel[]
     */
    private function buildIngredients(Recipe $recipe): array
    {
        $ingredients = [];

        foreach ($recipe->getIngredients() as $ingredient) {
            $ingredients[] = new IngredientModel(
                id: $ingredient->getId(),
                amount: $ingredient->getAmount(),
                itemId: $ingredient->getItemId(),
                itemName: $ingredient->getItem()->getName()
            );
        }

        return $ingredients;
    }

    /**
     * @return RecipeResultModel[]
     */
    private function buildResults(Recipe $recipe): array
    {
        $results = [];

        foreach ($recipe->getResults() as $result) {
            $resultItem = $result->getItem();

            $results[] = new RecipeResultModel(
                id: $result->getId(),
                amount: $result->getAmount(),
                itemId: $resultItem->getId(),
                itemName: $resultItem->getName()
            );
        }

        return $results;
    }
}
