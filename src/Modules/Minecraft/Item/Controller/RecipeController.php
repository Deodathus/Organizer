<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Controller;

use App\Modules\Minecraft\Item\DTO\Recipe\IngredientDTO;
use App\Modules\Minecraft\Item\DTO\Recipe\RecipeResultDTO;
use App\Modules\Minecraft\Item\DTO\Recipe\StoreRecipeDTO;
use App\Modules\Minecraft\Item\Exception\RecipeDoesNotExist;
use App\Modules\Minecraft\Item\Request\Recipe\RecipeStoreRequest;
use App\Modules\Minecraft\Item\Service\Factory\RecipeModelFactoryInterface;
use App\Modules\Minecraft\Item\Service\Recipe\RecipeServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RecipeController extends AbstractController
{
    public function __construct(
        private RecipeServiceInterface $recipeService,
        private RecipeModelFactoryInterface $recipeModelFactory
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
        $ingredients = [];
        $results = [];
        $itemInRecipeIds = [];

        foreach ($request->ingredients as $ingredient) {
            $ingredients[] = new IngredientDTO(amount: $ingredient['amount'], itemId: $ingredient['itemId']);

            $itemId = (int) $ingredient['itemId'];
            $itemInRecipeIds[$itemId] = $itemId;
        }

        foreach ($request->results as $result) {
            $results[] = new RecipeResultDTO(amount: $result['amount'], itemId: $result['itemId']);

            $itemId = (int) $result['itemId'];
            $itemInRecipeIds[$itemId] = $itemId;
        }

        return new JsonResponse(
            [
                'id' => $this->recipeService->store(
                    new StoreRecipeDTO(
                        name: $request->name,
                        ingredients: $ingredients,
                        results: $results,
                        itemsInRecipeIds: $itemInRecipeIds
                    )
                )
            ],
            Response::HTTP_CREATED
        );
    }
}
