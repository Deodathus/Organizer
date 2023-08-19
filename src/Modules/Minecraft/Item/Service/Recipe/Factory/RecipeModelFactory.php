<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Recipe\Factory;

use App\Modules\Minecraft\Item\Entity\Ingredient;
use App\Modules\Minecraft\Item\Entity\Recipe;
use App\Modules\Minecraft\Item\Entity\RecipeResult;
use App\Modules\Minecraft\Item\Response\Model\IngredientItemModel;
use App\Modules\Minecraft\Item\Response\Model\IngredientModel;
use App\Modules\Minecraft\Item\Response\Model\RecipeModel;
use App\Modules\Minecraft\Item\Response\Model\RecipeResultModel;

final class RecipeModelFactory implements RecipeModelFactoryInterface
{
    public function build(Recipe $recipe): RecipeModel
    {
        $ingredients = [];
        $recipeResults = [];

        /** @var Ingredient $ingredient */
        foreach ($recipe->getIngredients() as $ingredient) {
            $ingredientItems = [];

            foreach ($ingredient->getItems() as $item) {
                $ingredientItems[] = new IngredientItemModel(
                    $ingredient->getId(),
                    $ingredient->getAmount(),
                    $item->getId(),
                    $item->getName()
                );
            }

            $ingredients[] = new IngredientModel($ingredientItems);
        }

        /** @var RecipeResult $result */
        foreach ($recipe->getResults() as $result) {
            $item = $result->getItem();

            $recipeResults[] = new RecipeResultModel(
                $result->getId(),
                $result->getAmount(),
                $item->getId(),
                $item->getName()
            );
        }

        return new RecipeModel(
            id: $recipe->getId(),
            name: $recipe->getName(),
            ingredients: $ingredients,
            results: $recipeResults
        );
    }
}
