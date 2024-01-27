<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\TypeMedia;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(Request $request, TrickRepository $trickRepository): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $maxOffset = \count($trickRepository->findAll());
        if ($offset > $maxOffset) {
            $offset = (int) (ceil($maxOffset / TrickRepository::PAGINATOR_PER_PAGE) - 1) * TrickRepository::PAGINATOR_PER_PAGE;
        }
        $pagedTricks = $trickRepository->getTrickPaginator($offset);

        /**
         * @var Trick $trick
         */
        foreach ($pagedTricks as $trick) {
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
            'tricks'          => $pagedTricks,
            'offset'          => $offset,
            'previous'        => $offset - TrickRepository::PAGINATOR_PER_PAGE,
            'next'            => min(\count($pagedTricks), $offset + TrickRepository::PAGINATOR_PER_PAGE),
            'maxOffset'       => $maxOffset,
        ]);
    }
}
