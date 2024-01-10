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
    public function home(TrickRepository $tricks): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'tricks'          => $tricks->findAll(),
        ]);
    }
}
