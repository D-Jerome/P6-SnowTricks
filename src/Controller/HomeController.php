<?php

declare(strict_types=1);

namespace App\Controller;

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
        $mainPicture = null;
        foreach ($tricks as $trick) {
            $medias = $trick->getMedias();

            foreach ($medias as $media) {
                if ('picture' === $media->getTypeMedia()) {
                    $mainPicture = $media->getPath();

                    break;
                }
            }
        }

        return $this->render('home/index.html.twig', [
            'tricks'          => $tricks,
            'mainPicture'     => $mainPicture,
        ]);
    }
}
