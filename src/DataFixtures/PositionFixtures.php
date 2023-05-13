<?php

namespace App\DataFixtures;

use App\Entity\Position;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class PositionFixtures implements FixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create();

        for ($i = 0; $i < 20; $i++) {
            $position = new Position();
            $position->setName($faker->words(2));
            $position->setDescription($faker->words(6));
            $manager->persist($position);
        }

        $manager->flush();
    }
}
