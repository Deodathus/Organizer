<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Controller;

use App\Modules\Minecraft\Item\Exception\RecipeDoesNotExist;
use App\Modules\Minecraft\Item\Exception\RecipeStoreException;
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
        private readonly ArrayToRecipeTransformerInterface $toRecipeTransformer
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
}
