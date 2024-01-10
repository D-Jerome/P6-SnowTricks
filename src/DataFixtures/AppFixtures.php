<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 50; ++$i) {
            $trick = new Trick();
            $slug = $this->faker->slug(3, false);
            $name = str_replace('-', ' ', $slug);
            $trick->setName($name)
                ->setSlug($slug)
                ->setDescription($this->faker->paragraph(5, true))
            ;
            $manager->persist($trick);
        }

        $manager->flush();
    }
}
