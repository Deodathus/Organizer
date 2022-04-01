<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Controller;

use App\Modules\Minecraft\Item\DTO\SaveItemDTO;
use App\Modules\Minecraft\Item\Exception\ItemDoesNotExist;
use App\Modules\Minecraft\Item\Response\Model\ItemModel;
use App\Modules\Minecraft\Item\Service\ItemServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ItemController extends AbstractController
{
    public function __construct(private ItemServiceInterface $itemService){}

    public function fetch(int $id): JsonResponse
    {
        try {
            $item = $this->itemService->fetch($id);

            return new JsonResponse(
                (new ItemModel($item->getId(), $item->getKey(), $item->getSubKey(), $item->getName()))->toArray()
            );
        } catch (ItemDoesNotExist $exception) {
            return new JsonResponse(['id' => $id], Response::HTTP_NOT_FOUND);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $itemDTO = new SaveItemDTO(
            key: (int) $request->request->get('key'),
            subKey: (int) $request->request->get('subKey'),
            name: $request->request->get('name')
        );

        return new JsonResponse(
            [
                'id' => $this->itemService->store($itemDTO),
            ],
            Response::HTTP_CREATED
        );
    }
}
