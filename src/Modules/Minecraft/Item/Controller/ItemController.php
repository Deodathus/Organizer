<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Controller;

use App\Modules\Minecraft\Item\DTO\Item\StoreItemDTO;
use App\Modules\Minecraft\Item\Exception\ItemDoesNotExist;
use App\Modules\Minecraft\Item\Request\Item\ItemStoreRequest;
use App\Modules\Minecraft\Item\Response\Model\ItemModel;
use App\Modules\Minecraft\Item\Service\Item\ItemServiceInterface;
use App\Modules\Minecraft\Item\Service\Transformer\ItemToItemRecipesModelTransformerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class ItemController extends AbstractController
{
    public function __construct(
        private readonly ItemServiceInterface $itemService,
        private readonly ItemToItemRecipesModelTransformerInterface $toItemRecipesModelTransformer
    ) {}

    public function fetch(int $id): JsonResponse
    {
        try {
            $item = $this->itemService->fetch($id);
        } catch (ItemDoesNotExist $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(
            (new ItemModel($item->getId(), $item->getKey(), $item->getSubKey(), $item->getName()))->toArray()
        );
    }

    public function fetchAll(): JsonResponse
    {
        $result = [];

        foreach ($this->itemService->fetchAll() as $item) {
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
                $this->itemService->fetch($id)
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
                'id' => $this->itemService->store($itemDTO),
            ],
            Response::HTTP_CREATED
        );
    }

    public function delete(int $id): JsonResponse
    {
        $this->itemService->deleteById($id);

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
