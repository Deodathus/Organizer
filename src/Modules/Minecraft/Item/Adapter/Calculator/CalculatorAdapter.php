<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Adapter\Calculator;

use App\Modules\Minecraft\CraftCalculator\Service\Calculator\CalculatorInterface;
use App\Modules\Minecraft\CraftCalculator\Service\Calculator\TreeRecipeCalculatorInterface;
use App\Modules\Minecraft\Item\Adapter\Calculator\Model\Calculable;
use App\Modules\Minecraft\Item\DTO\Recipe\Calculated\CalculatorResult;
use App\Modules\Minecraft\Item\DTO\Recipe\CalculatedTree\TreeCalculatorResult;
use App\Modules\Minecraft\Item\Entity\Recipe;
use App\Modules\Minecraft\Item\Factory\Recipe\Calculator\CalculatorResultFactory;
use App\Modules\Minecraft\Item\Factory\Recipe\Calculator\TreeCalculatorResultFactory;

final class CalculatorAdapter implements CalculatorAdapterInterface
{
    public function __construct(
        private readonly CalculatorInterface $calculator,
        private readonly TreeRecipeCalculatorInterface $treeRecipeCalculator
    ) {}

    public function calculate(Recipe $recipe, int $amount): CalculatorResult
    {
        $calculable = new Calculable($recipe);

        $calculatorResult = $this->calculator->calculate($calculable, $amount);

        return CalculatorResultFactory::buildFromExternalResult($calculatorResult);
    }

    public function calculateWithTree(Recipe $recipe, int $amount): TreeCalculatorResult
    {
        $calculable = new Calculable($recipe);

        $treeCalculatorResult = $this->treeRecipeCalculator->calculate($calculable, $amount);

        return TreeCalculatorResultFactory::buildFromExternalResult($treeCalculatorResult);
    }
}
