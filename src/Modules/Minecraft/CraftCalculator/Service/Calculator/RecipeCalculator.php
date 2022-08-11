<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\CraftCalculator\Service\Calculator;

use App\Modules\Minecraft\CraftCalculator\DTO\CalculatorResult;
use App\Modules\Minecraft\CraftCalculator\DTO\IngredientDTO;
use App\Modules\Minecraft\CraftCalculator\DTO\IngredientItemDTO;
use App\Modules\Minecraft\CraftCalculator\DTO\ResultDTO;
use App\Modules\Minecraft\CraftCalculator\Model\CalculableInterface;
use App\Modules\Minecraft\Item\Entity\Ingredient;
use App\Modules\Minecraft\Item\Entity\RecipeResult;
use Doctrine\Common\Collections\Collection;

final class RecipeCalculator implements CalculatorInterface
{
    public function calculate(CalculableInterface $calculable, int $amount): CalculatorResult
    {
        $ingredients = $this->calculateIngredients($calculable->getIngredients(), $amount);
        $results = $this->calculateResults($calculable->getResults(), $amount);

        return new CalculatorResult($calculable->getId(), $ingredients, $results);
    }

    /**
     * @param Collection<Ingredient> $ingredients
     *
     * @return IngredientDTO[]
     */
    private function calculateIngredients(Collection $ingredients, int $amount): array
    {
        $result = [];

        foreach ($ingredients as $ingredient) {
            $ingredientItems = [];

            foreach ($ingredient->getItems() as $item) {
                $ingredientItems[] = new IngredientItemDTO(
                    $ingredient->getId(),
                    $ingredient->getAmount() * $amount,
                    $item->getId(),
                    $item->getName()
                );
            }

            $result[] = new IngredientDTO($ingredientItems);
        }

        return $result;
    }

    /**
     * @param Collection<RecipeResult> $results
     *
     * @return ResultDTO[]
     */
    private function calculateResults(Collection $results, int $amount): array
    {
        $result = [];

        foreach ($results as $recipeResult) {
            $resultAmount = $recipeResult->getAmount() * $amount;

            $result[] = new ResultDTO(
                id: $recipeResult->getId(),
                amount: $resultAmount,
                itemId: $recipeResult->getItemId(),
                itemName: $recipeResult->getItemName()
            );
        }

        return $result;
    }
}
