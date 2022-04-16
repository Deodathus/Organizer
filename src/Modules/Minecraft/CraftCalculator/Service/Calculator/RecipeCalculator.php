<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\CraftCalculator\Service\Calculator;

use App\Modules\Minecraft\CraftCalculator\DTO\CalculatorResult;
use App\Modules\Minecraft\CraftCalculator\DTO\IngredientDTO;
use App\Modules\Minecraft\CraftCalculator\Entity\Calculable;
use App\Modules\Minecraft\Item\Entity\Recipe;
use Doctrine\Common\Collections\Collection;

final class RecipeCalculator implements CalculatorInterface
{
    public function calculate(Recipe|Calculable $calculable, int $amount): CalculatorResult
    {
        $ingredients = $this->calculateIngredients($calculable->getIngredients(), $amount);

        return new CalculatorResult($calculable->getId(), $ingredients);
    }

    private function calculateIngredients(Collection $ingredients, int $amount): array
    {
        $result = [];

        foreach ($ingredients as $ingredient) {
            $ingredientAmount = $ingredient->getAmount() * $amount;

            $result[] = new IngredientDTO(amount: $ingredientAmount, itemId: $ingredient->getItemId());
        }

        return $result;
    }
}
