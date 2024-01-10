<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    #[Route('/trick/{slug<((\w+)-){0,}(\w+)>?null}', name: 'app_trick')]
    public function showTrick(string $slug): Response
    {
        return $this->render('trick/index.html.twig', [
            'controller_name' => 'TrickController',
            'slug'            => $slug,
        ]);
    }
}
