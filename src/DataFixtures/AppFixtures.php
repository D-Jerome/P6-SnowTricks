<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 5; ++$i) {
            $category = new Category();
            $category->setName($faker->sentence(3));
            $manager->persist($category);

            for ($j = 0; $j < random_int(0, 10); ++$j) {
                $trick = new Trick();
                $slug = $faker->slug(3, false);
                $name = str_replace('-', ' ', $slug);
                $trick->setName($faker->sentence(8, true))
                    ->setDescription($faker->paragraph(5, true))
                    ->setCategory($category)
                ;
                $manager->persist($trick);
                for ($h = 0; $h < 5; ++$h) {
                    $comment = new Comment();
                    $comment->setContent($faker->sentence(8, true))
                        ->setTrick($trick)
                    ;
                    $manager->persist($comment);
                }
            }
        }

        $manager->flush();
    }
}
