<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Controller;

use App\Modules\Minecraft\Item\DTO\Item\StoreItemDTO;
use App\Modules\Minecraft\Item\Enum\ItemTypes;
use App\Modules\Minecraft\Item\Exception\ItemDoesNotExist;
use App\Modules\Minecraft\Item\Request\Item\FetchAllItemsRequest;
use App\Modules\Minecraft\Item\Request\Item\FetchItemRecipesRequest;
use App\Modules\Minecraft\Item\Request\Item\ItemStoreRequest;
use App\Modules\Minecraft\Item\Search\FilterBus;
use App\Modules\Minecraft\Item\Service\Item\Factory\ItemModelFactory;
use App\Modules\Minecraft\Item\Service\Item\ItemFetcher;
use App\Modules\Minecraft\Item\Service\Item\ItemPersister;
use App\Modules\Minecraft\Item\Service\Item\ItemRemover;
use App\Modules\Minecraft\Item\Service\Recipe\RecipeFetcher;
use App\Modules\Minecraft\Item\Service\Transformer\ItemToItemRecipesModelTransformerInterface;
use App\Modules\Minecraft\Item\Service\Transformer\RecipeToRecipeModelTransformerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class ItemController extends AbstractController
{
    public function __construct(
        private readonly ItemFetcher $itemFetcher,
        private readonly ItemPersister $itemPersister,
        private readonly ItemRemover $itemRemover,
<<<<<<< HEAD
        private readonly ItemToItemRecipesModelTransformerInterface $toItemRecipesModelTransformer
    ) {
    }
=======
        private readonly RecipeFetcher $recipeFetcher,
        private readonly ItemToItemRecipesModelTransformerInterface $toItemRecipesModelTransformer,
        private readonly RecipeToRecipeModelTransformerInterface $recipeToRecipeModelTransformer,
        private readonly ItemModelFactory $itemModelFactory
    ) {}
>>>>>>> Added new endpoints with pagination for item recipes. Code refactoring.

    public function fetch(int $id): JsonResponse
    {
        try {
            $item = $this->itemFetcher->fetch($id);
        } catch (ItemDoesNotExist $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(
            $this->itemModelFactory
                ->build($item)
                ->toArray()
        );
    }

    public function fetchAll(FetchAllItemsRequest $request): JsonResponse
    {
        $result = [];

        $filterBus = new FilterBus(
            perPage: $request->getPerPage(),
            page: $request->getPage(),
            searchPhrase: $request->getSearchPhrase(),
        );

        $paginatedResult = $this->itemFetcher->fetchAllPaginated($filterBus);

        foreach ($paginatedResult->getResult() as $item) {
            $result[] = $this->itemModelFactory
                ->build($item)
                ->toArray();
        }

        return new JsonResponse(
            $result,
            Response::HTTP_OK,
            [
                'X-Total-Count' => $paginatedResult->getTotalCount(),
                'X-Total-Pages' => $paginatedResult->getTotalPages(),
            ]
        );
    }

    /**
     * @deprecated
     */
    public function fetchRecipes(int $id): JsonResponse
    {
        try {
            $itemRecipeModel = $this->toItemRecipesModelTransformer->transform(
                $this->itemFetcher->fetch($id)
            );
        } catch (ItemDoesNotExist $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($itemRecipeModel->toArray());
    }

    public function fetchRecipesWhereUsingAsIngredient(FetchItemRecipesRequest $itemRecipesRequest): JsonResponse
    {
        $filterBus = new FilterBus(
            $itemRecipesRequest->perPage,
            $itemRecipesRequest->page,
            null
        );

        $ingredientsRecipes = $this->recipeFetcher->fetchByItemIngredientId($itemRecipesRequest->itemId, $filterBus);

        $recipes = $this->recipeToRecipeModelTransformer->transform($ingredientsRecipes);

        return new JsonResponse(
            array_map(static fn($recipe): array => $recipe->toArray(), $recipes->toArray()),
            Response::HTTP_OK,
            [
                'X-Total-Count' => $ingredientsRecipes->getTotalCount(),
                'X-Total-Pages' => $ingredientsRecipes->getTotalPages(),
            ]
        );
    }

    public function fetchRecipesWhereUsingAsResult(FetchItemRecipesRequest $itemRecipesRequest): JsonResponse
    {
        $filterBus = new FilterBus(
            $itemRecipesRequest->perPage,
            $itemRecipesRequest->page,
            null
        );

        $resultsRecipes = $this->recipeFetcher->fetchByItemResultId($itemRecipesRequest->itemId, $filterBus);

        $recipes = $this->recipeToRecipeModelTransformer->transform($resultsRecipes);

        return new JsonResponse(
            array_map(static fn($recipe): array => $recipe->toArray(), $recipes->toArray()),
            Response::HTTP_OK,
            [
                'X-Total-Count' => $resultsRecipes->getTotalCount(),
                'X-Total-Pages' => $resultsRecipes->getTotalPages(),
            ]
        );
    }

    public function store(ItemStoreRequest $request): JsonResponse
    {
        $itemDTO = new StoreItemDTO(
            itemType: ItemTypes::ITEM,
            key: $request->key,
            subKey: $request->subKey,
            name: $request->name,
            itemTag: $request->itemTag,
            fluidName: ''
        );

        return new JsonResponse(
            [
                'id' => $this->itemPersister->store($itemDTO)->getId(),
            ],
            Response::HTTP_CREATED
        );
    }

    public function delete(int $id): JsonResponse
    {
        $this->itemRemover->deleteById($id);

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
