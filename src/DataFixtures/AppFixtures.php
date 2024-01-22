<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Media;
use App\Entity\Trick;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($g = 0; $g < 5; ++$g) {
            $category = new Category();
            $category->setName($faker->sentence(3));
            $manager->persist($category);

            for ($i = 0; $i < 2; ++$i) {
                $user = new User();
                $user->setUsername($faker->userName());
                $user->setEmail($faker->safeEmail());
                $user->setPassword($faker->password());
                $manager->persist($user);

                for ($j = 0; $j < random_int(0, 10); ++$j) {
                    $trick = new Trick();
                    $slug = $faker->slug(3, false);
                    $name = str_replace('-', ' ', $slug);
                    $trick->setName($faker->sentence(8, true))
                        ->setDescription($faker->paragraphs(20, true))
                        ->setCategory($category)
                        ->setUser($user)
                    ;
                    $manager->persist($trick);
                    for ($h = 0; $h < 5; ++$h) {
                        $comment = new Comment();
                        $comment->setContent($faker->sentence(8, true))
                            ->setTrick($trick)
                            ->setUser($user)
                        ;
                        $manager->persist($comment);
                    }

                    for($k = 0; $k < random_int(1, 3); ++$k) {
                        $media = new Media();
                        $media->setDescription($faker->sentence(4, true))
                            ->setPath('DefaultMedia.png')
                            ->setTypeMedia('picture')
                            ->setTrick($trick)
                        ;
                        $manager->persist($media);
                    }

                    for($k = 0; $k < random_int(1, 3); ++$k) {
                        $media = new Media();
                        $media->setDescription($faker->sentence(3, true))
                            ->setPath('DefaultMedia2.webp')
                            ->setTypeMedia('picture')
                            ->setTrick($trick)
                        ;
                        $manager->persist($media);
                    }
                }
            }
        }
        $manager->flush();
    }
}
