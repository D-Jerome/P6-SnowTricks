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

        $categories = ['Grabs', 'Rotations', 'Flips', 'Slides'];

        $figures = ['Melancholie', 'Mute', 'Style week', '540 rotation', 'Indy', 'Stalefish', 'Japan Air', 'Nose grab', '180 rotation', 'Sad', 'Tail grab', '900 rotation', 'Seat Belt', '360 rotation', 'Japan', '720 rotation', 'Backside Air', 'Truck driver', 'Big foot', 'Slide', 'Rocket Air', 'Flip', 'Method Air'];

        $videos = [
            '<iframe width="560" height="315" src="https://www.youtube.com/embed/geB_HeU6m0k?si=f32pPPS-o5Zn_8y4" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>',
            '<iframe width="560" height="315" src="https://www.youtube.com/embed/_Pq70pqJxKI?si=heLyZdbd_i-NPSkZ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>',
            '<iframe width="560" height="315" src="https://www.youtube.com/embed/UGdif-dwu-8?si=rQ-er_F0-IItfWvj" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>',
            '<iframe width="560" height="315" src="https://www.youtube.com/embed/9a4C8NcbmME?si=Ua9WIGJWFXLbXzAN" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>',
            '<iframe width="560" height="315" src="https://www.youtube.com/embed/RmoTk02rIxE?si=HHOuAtakTGMhWYBq" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>',
            '<iframe width="560" height="315" src="https://www.youtube.com/embed/yKovI9hMjBs?si=khyHLZ36rpJgwO5Q" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>',
        ];

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

        foreach ($categories as $cat) {
            $category = new Category();
            $category->setName($cat);
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

                    for($k = 0; $k < random_int(0, 1); ++$k) {
                        $media = new Media();
                        $media->setDescription($faker->sentence(4, true))
                            ->setPath('default/DefaultMedia.png')
                            ->setTypeMedia(TypeMedia::Image)
                            ->setTrick($trick)
                        ;
                        $this->em->persist($media);
                    }
                    for($k = 0; $k < random_int(0, 1); ++$k) {
                        $media = new Media();
                        $media->setDescription($faker->sentence(4, true))
                            ->setPath('default/Trick-Japan-Grab.jpg')
                            ->setTypeMedia(TypeMedia::Image)
                            ->setTrick($trick)
                        ;
                        $this->em->persist($media);
                    }
                    for($k = 0; $k < random_int(0, 1); ++$k) {
                        $media = new Media();
                        $media->setDescription($faker->sentence(4, true))
                            ->setPath('default/Trick-Nose-Grab.jpg')
                            ->setTypeMedia(TypeMedia::Image)
                            ->setTrick($trick)
                        ;
                        $this->em->persist($media);
                    }
                    for($k = 0; $k < random_int(0, 1); ++$k) {
                        $media = new Media();
                        $media->setDescription($faker->sentence(4, true))
                            ->setPath('default/Trick-suitcase-Grab.jpg')
                            ->setTypeMedia(TypeMedia::Image)
                            ->setTrick($trick)
                        ;
                        $this->em->persist($media);
                    }
                    for($k = 0; $k < random_int(0, 1); ++$k) {
                        $media = new Media();
                        $media->setDescription($faker->sentence(4, true))
                            ->setPath('default/Trick-Tail-Grab.jpg')
                            ->setTypeMedia(TypeMedia::Image)
                            ->setTrick($trick)
                        ;
                        $this->em->persist($media);
                    }

                    for($k = 0; $k < random_int(0, 1); ++$k) {
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
                        $path = $videos[array_rand($videos)];
                        $media->setDescription($faker->sentence(3, true))
                            ->setPath($path)
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
