<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Controller;

use App\Modules\Minecraft\Item\Entity\Item;
use App\Modules\Minecraft\Item\Response\Model\ItemModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ItemController extends AbstractController
{
    public function index(Request $request): JsonResponse
    {
        $entity = new Item('Test item', 2);

        return new JsonResponse(
            (new ItemModel($entity->getId(), $entity->getSubId(), $entity->getName()))->toArray()
        );
    }
}
