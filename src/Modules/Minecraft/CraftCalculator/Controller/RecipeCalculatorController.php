<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\CraftCalculator\Controller;

use App\Modules\Minecraft\CraftCalculator\Request\Recipe\RecipeCalculateRequest;
use App\Modules\Minecraft\CraftCalculator\Service\Calculator\CalculatorInterface;
use App\Modules\Minecraft\CraftCalculator\Service\Calculator\TreeRecipeCalculatorInterface;
use App\Modules\Minecraft\Item\Contract\Recipe\Exception\RecipeNotFound;
use App\Modules\Minecraft\Item\Contract\Recipe\Service\RecipeContractInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class RecipeCalculatorController extends AbstractController
{
    public function __construct(
        private readonly RecipeContractInterface $recipeContract,
        private readonly CalculatorInterface $calculator,
        private readonly TreeRecipeCalculatorInterface $treeCalculator
    ) {}

    public function calculate(RecipeCalculateRequest $calculateRequest): JsonResponse
    {
        try {
            $recipe = $this->recipeContract->fetch($calculateRequest->getRecipeId());
        } catch (RecipeNotFound $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }

        $calculatorResult = $this->calculator->calculate($recipe, $calculateRequest->getAmount());

        return new JsonResponse($calculatorResult->toArray());
    }

    public function calculateWithTree(RecipeCalculateRequest $calculateRequest): JsonResponse
    {
        try {
            $recipe = $this->recipeContract->fetch($calculateRequest->getRecipeId());
        } catch (RecipeNotFound $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }

        $calculatorResult = $this->treeCalculator->calculate($recipe, $calculateRequest->getAmount());

        return new JsonResponse($calculatorResult->toArray());
    }
}
