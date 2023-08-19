<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Fixture\Item;

use App\DataFixtures\BaseFixture;
use App\Modules\Minecraft\Item\Entity\Item;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

final class ItemFixture extends BaseFixture
{
    private const AMOUNT = 60;

    protected function prepareLoader(ObjectManager $manager): void
    {
        $this->createMany(
            self::AMOUNT,
            static function (Generator $faker, ItemFixture $fixture) {
                $item = new Item(
                    $faker->userName(),
                    $itemKey = $faker->numberBetween(1, 10000),
                    $itemSubKey = $faker->numberBetween(1, 10000),
                    $faker->userName()
                );

                $fixture->addReference(
                    $itemKey + $itemSubKey,
                    $item
                );

                return $item;
            },
            $manager
        );
    }
}
