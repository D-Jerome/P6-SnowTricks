<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\TypeMedia;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(TrickRepository $trickRepository): Response
    {
        $tricks = $trickRepository->findAll();

        foreach ($tricks as $trick) {
            foreach ($trick->getMedias() as $media) {
                if (TypeMedia::Image === $media->getTypeMedia()) {
                    $randomPicture = $media->getPath();
                    if ($randomPicture) {
                        $trick->setMainPicture($randomPicture);
                        break;
                    }
                }
            }
        }

        return $this->render('home/index.html.twig', [
            'tricks'          => $tricks,
        ]);
    }
}
