<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Controller;

use App\Modules\Minecraft\Item\DTO\Item\StoreItemDTO;
use App\Modules\Minecraft\Item\Entity\Fluid;
use App\Modules\Minecraft\Item\Entity\FluidCell;
use App\Modules\Minecraft\Item\Enum\ItemTypes;
use App\Modules\Minecraft\Item\Exception\ItemDoesNotExist;
use App\Modules\Minecraft\Item\Request\Item\FetchAllItemsRequest;
use App\Modules\Minecraft\Item\Request\Item\FetchItemRecipesRequest;
use App\Modules\Minecraft\Item\Request\Item\ItemStoreRequest;
use App\Modules\Minecraft\Item\Response\Model\ItemModel;
use App\Modules\Minecraft\Item\Search\FilterBus;
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
        private readonly RecipeToRecipeModelTransformerInterface $recipeToRecipeModelTransformer
    ) {}
>>>>>>> Added new endpoints with pagination for item recipes. Code refactoring.

    public function fetch(int $id): JsonResponse
    {
        try {
            $item = $this->itemFetcher->fetch($id);
        } catch (ItemDoesNotExist $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }

        $fluidName = match (get_class($item)) {
            default => null,
            FluidCell::class => $item->getFluidName(),
            Fluid::class => $item->getFluidName(),
        };

        return new JsonResponse(
            (new ItemModel(
                $item->getId(),
                $item->getDiscriminator(),
                $item->getKey(),
                $item->getSubKey(),
                $item->getName(),
                $item->getItemTag(),
                $fluidName
            ))->toArray()
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
            $fluidName = match (get_class($item)) {
                default => null,
                FluidCell::class => $item->getFluidName(),
                Fluid::class => $item->getFluidName(),
            };

            $result[] = (new ItemModel(
                $item->getId(),
                $item->getDiscriminator(),
                $item->getKey(),
                $item->getSubKey(),
                $item->getName(),
                $item->getItemTag(),
                $fluidName
            ))->toArray();
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

        $ingredients = $this->recipeFetcher->fetchByItemIngredientId($itemRecipesRequest->itemId, $filterBus);

        $recipes = $this->recipeToRecipeModelTransformer->transform($ingredients);

        $result = [];
        foreach ($recipes as $recipe) {
            $result[] = $recipe->toArray();
        }

        return new JsonResponse(
            $result,
            Response::HTTP_OK,
            [
                'X-Total-Count' => $ingredients->getTotalCount(),
                'X-Total-Pages' => $ingredients->getTotalPages(),
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

        $ingredients = $this->recipeFetcher->fetchByItemResultId($itemRecipesRequest->itemId, $filterBus);

        $recipes = $this->recipeToRecipeModelTransformer->transform($ingredients);

        $result = [];
        foreach ($recipes as $recipe) {
            $result[] = $recipe->toArray();
        }

        return new JsonResponse(
            $result,
            Response::HTTP_OK,
            [
                'X-Total-Count' => $ingredients->getTotalCount(),
                'X-Total-Pages' => $ingredients->getTotalPages(),
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
