<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Controller;

use App\Modules\Minecraft\Item\Adapter\Calculator\CalculatorAdapterInterface;
use App\Modules\Minecraft\Item\Exception\RecipeDoesNotExist;
use App\Modules\Minecraft\Item\Exception\RecipeStoreException;
use App\Modules\Minecraft\Item\Request\Recipe\RecipeCalculateRequest;
use App\Modules\Minecraft\Item\Request\Recipe\RecipeStoreRequest;
use App\Modules\Minecraft\Item\Service\Factory\RecipeModelFactoryInterface;
use App\Modules\Minecraft\Item\Service\Recipe\RecipeServiceInterface;
use App\Modules\Minecraft\Item\Service\Transformer\ArrayToRecipeTransformerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class RecipeController extends AbstractController
{
    public function __construct(
        private readonly RecipeServiceInterface $recipeService,
        private readonly RecipeModelFactoryInterface $recipeModelFactory,
        private readonly ArrayToRecipeTransformerInterface $toRecipeTransformer,
        private readonly CalculatorAdapterInterface $calculator
    ) {}

    public function fetch(int $id): JsonResponse
    {
        try {
            $recipe = $this->recipeService->fetch($id);

            return new JsonResponse(
                $this->recipeModelFactory->build($recipe)->toArray()
            );
        } catch (RecipeDoesNotExist $exception) {
            return new JsonResponse(
                [
                    'error' => $exception->getMessage(),
                ],
                Response::HTTP_NOT_FOUND
            );
        }
    }

    public function store(RecipeStoreRequest $request): JsonResponse
    {
        try {
            $recipeId = $this->recipeService->store(
                $this->toRecipeTransformer->transform($request->toArray())
            );
        } catch (RecipeStoreException $exception) {
            return new JsonResponse(
                [
                    'error' => $exception->getMessage(),
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        return new JsonResponse(
            [
                'id' => $recipeId,
            ],
            Response::HTTP_CREATED
        );
    }

    public function calculate(RecipeCalculateRequest $calculateRequest): JsonResponse
    {
        try {
            $recipe = $this->recipeService->fetch($calculateRequest->getRecipeId());
        } catch (RecipeDoesNotExist $exception) {
            return new JsonResponse(
                [
                    'error' => $exception->getMessage(),
                ],
                Response::HTTP_NOT_FOUND
            );
        }

        $recipeResult = $this->calculator->calculate($recipe, $calculateRequest->getAmount());

        return new JsonResponse($recipeResult->toArray());
    }

    public function calculateWithTree(RecipeCalculateRequest $calculateRequest): JsonResponse
    {
        try {
            $recipe = $this->recipeService->fetch($calculateRequest->getRecipeId());
        } catch (RecipeDoesNotExist $exception) {
            return new JsonResponse(
                [
                    'error' => $exception->getMessage(),
                ],
                Response::HTTP_NOT_FOUND
            );
        }

        $recipeResult = $this->calculator->calculateWithTree($recipe, $calculateRequest->getAmount());

        return new JsonResponse($recipeResult->toArray());
    }
}
