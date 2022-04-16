<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\CraftCalculator\Controller;

use App\Modules\Minecraft\CraftCalculator\Request\Recipe\RecipeCalculateRequest;
use App\Modules\Minecraft\CraftCalculator\Service\Calculator\CalculatorInterface;
use App\Modules\Minecraft\Item\Contract\Recipe\Exception\RecipeNotFound;
use App\Modules\Minecraft\Item\Contract\Recipe\Service\RecipeContractInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RecipeCalculatorController extends AbstractController
{
    public function __construct(private RecipeContractInterface $recipeContract, private CalculatorInterface $calculator) {}

    public function calculate(RecipeCalculateRequest $calculateRequest): JsonResponse
    {
        try {
            $recipe = $this->recipeContract->fetch($calculateRequest->getRecipeId());
        } catch (RecipeNotFound $exception) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        $calculatorResult = $this->calculator->calculate($recipe, $calculateRequest->getAmount());

        return new JsonResponse($calculatorResult->toArray());
    }
}
