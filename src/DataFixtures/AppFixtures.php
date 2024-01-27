<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Media;
use App\Entity\Trick;
use App\Entity\TypeMedia;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Webmozart\Assert\Assert;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $userPasswordHasher;
    private EntityManagerInterface $em;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $em)
    {
        $this->userPasswordHasher = $userPasswordHasher;

        $this->em = $em;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Admin
        $admin = new User();
        $admin->setEmail('admin@snowtricks.fr')
            ->setActive(true)
            ->setUsername('admin')
            ->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $admin,
                    'admin'
                )
            )
        ;

        $this->em->persist($admin);
        $this->em->flush();

        for ($g = 0; $g < 5; ++$g) {
            $category = new Category();
            $category->setName($faker->sentence(3));
            $this->em->persist($category);

            for ($i = 0; $i < 2; ++$i) {
                $user = new User();
                $user->setUsername($faker->userName());
                $user->setEmail($faker->safeEmail());
                $user->setPassword($faker->password());
                $this->em->persist($user);

                for ($j = 0; $j < random_int(0, 10); ++$j) {
                    $description = $faker->paragraphs(20, true);

                    Assert::string($description);
                    $trick = new Trick();
                    $slug = $faker->slug(3, false);
                    $name = str_replace('-', ' ', $slug);
                    $trick->setName($name)
                        ->setDescription($description)
                        ->setCategory($category)
                        ->setUser($user)
                    ;
                    $this->em->persist($trick);
                    for ($h = 0; $h < 15; ++$h) {
                        $comment = new Comment();
                        $comment->setContent($faker->sentence(8, true))
                            ->setTrick($trick)
                            ->setUser($user)
                        ;
                        $this->em->persist($comment);
                    }

                    for($k = 0; $k < random_int(1, 3); ++$k) {
                        $media = new Media();
                        $media->setDescription($faker->sentence(4, true))
                            ->setPath('default/DefaultMedia.png')
                            ->setTypeMedia(TypeMedia::Image)
                            ->setTrick($trick)
                        ;
                        $this->em->persist($media);
                    }

                    for($k = 0; $k < random_int(1, 3); ++$k) {
                        $media = new Media();
                        $media->setDescription($faker->sentence(3, true))
                            ->setPath('default/DefaultMedia2.webp')
                            ->setTypeMedia(TypeMedia::Image)
                            ->setTrick($trick)
                        ;
                        $this->em->persist($media);
                    }

                    for($k = 0; $k < random_int(0, 2); ++$k) {
                        $media = new Media();
                        $media->setDescription($faker->sentence(3, true))
                            ->setPath('https://www.youtube.com/embed/ivO_fl0HrXs?si=S5MZuQ1NHOUZEend')
                            ->setTypeMedia(TypeMedia::Video)
                            ->setTrick($trick)
                        ;
                        $this->em->persist($media);
                    }
                }
            }
        }
        $this->em->flush();
    }
}
