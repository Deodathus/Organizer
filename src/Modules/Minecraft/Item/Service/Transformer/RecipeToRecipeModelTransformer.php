<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Transformer;

use App\Modules\Minecraft\Item\Entity\Recipe;
use App\Modules\Minecraft\Item\Response\Model\IngredientModel;
use App\Modules\Minecraft\Item\Response\Model\RecipeModel;
use App\Modules\Minecraft\Item\Response\Model\RecipeResultModel;
use App\Modules\Minecraft\Item\Search\PaginatedResult;
use Doctrine\Common\Collections\ArrayCollection;

final class RecipeToRecipeModelTransformer implements RecipeToRecipeModelTransformerInterface
{
    public function transform(PaginatedResult $recipes): ArrayCollection
    {
        $result = [];

        foreach ($recipes->getResult() as $ingredientRecipe) {
            $result[] = $this->buildRecipeModel($ingredientRecipe);
        }

        return new ArrayCollection($result);
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
