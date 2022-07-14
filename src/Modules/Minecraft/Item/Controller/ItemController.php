<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Controller;

use App\Modules\Minecraft\Item\DTO\Item\StoreItemDTO;
use App\Modules\Minecraft\Item\Exception\ItemDoesNotExist;
use App\Modules\Minecraft\Item\Request\Item\FetchAllItemsRequest;
use App\Modules\Minecraft\Item\Request\Item\ItemStoreRequest;
use App\Modules\Minecraft\Item\Response\Model\ItemModel;
use App\Modules\Minecraft\Item\Search\FilterBus;
use App\Modules\Minecraft\Item\Service\Item\ItemFetcher;
use App\Modules\Minecraft\Item\Service\Item\ItemPersister;
use App\Modules\Minecraft\Item\Service\Item\ItemRemover;
use App\Modules\Minecraft\Item\Service\Transformer\ItemToItemRecipesModelTransformerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class ItemController extends AbstractController
{
    public function __construct(
        private readonly ItemFetcher $itemFetcher,
        private readonly ItemPersister $itemPersister,
        private readonly ItemRemover $itemRemover,
        private readonly ItemToItemRecipesModelTransformerInterface $toItemRecipesModelTransformer
    ) {}

    public function fetch(int $id): JsonResponse
    {
        try {
            $item = $this->itemFetcher->fetch($id);
        } catch (ItemDoesNotExist $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(
            (new ItemModel($item->getId(), $item->getKey(), $item->getSubKey(), $item->getName()))->toArray()
        );
    }

    public function fetchAll(FetchAllItemsRequest $request): JsonResponse
    {
        $result = [];

        $filterBus = new FilterBus(
            perPage: $request->getPerPage(),
            page: $request->getPage()
        );

        foreach ($this->itemFetcher->fetchAllPaginated($filterBus) as $item) {
            $result[] = (new ItemModel(
                $item->getId(),
                $item->getKey(),
                $item->getSubKey(),
                $item->getName()
            ))->toArray();
        }

        return new JsonResponse($result);
    }

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

    public function store(ItemStoreRequest $request): JsonResponse
    {
        $itemDTO = new StoreItemDTO(
            key: $request->key,
            subKey: $request->subKey,
            name: $request->name
        );

        return new JsonResponse(
            [
                'id' => $this->itemPersister->store($itemDTO),
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
