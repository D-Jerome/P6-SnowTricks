<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    #[Route('/trick/{slug<((\w+)-){0,}(\w+)>?}', name: 'app_trick')]
    public function showTrick(string $slug): Response
    {

        return $this->render('trick/index.html.twig', [
            'controller_name' => 'TrickController',
            'slug' => $slug,
        ]);
    }
}
