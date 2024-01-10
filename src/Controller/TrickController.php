<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    #[Route('/trick/{slug<((\w+)-){0,}(\w+)>?null}', name: 'app_trick')]
    public function showTrick(string $slug, TrickRepository $trick): Response
    {
        return $this->render('trick/index.html.twig', [
            'controller_name'  => 'TrickController',
            'trick'            => $trick->findOneBySlug($slug),
        ]);
    }
}
