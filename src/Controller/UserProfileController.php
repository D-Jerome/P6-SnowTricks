<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\UpdatePasswordType;
use App\Form\UserAvatarType;
use App\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserProfileController extends AbstractController
{
    #[Route('/user/', name: 'app_user_profile')]
    public function showProfile(Request $request, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();

        $formProfile = $this->createForm(UserProfileType::class, $user);
        $formPassword = $this->createForm(UpdatePasswordType::class, $user);
        $formAvatar = $this->createForm(UserAvatarType::class, $user);

        $formProfile->handleRequest($request);
        $formPassword->handleRequest($request);
        $formAvatar->handleRequest($request);



        return $this->render('user_profile/show.html.twig', [
            'user' => $user,
            'formProfile' => $formProfile->createView(),
            'formPassword' => $formPassword->createView(),
            'formAvatar' => $formAvatar->createView(),
        ]);
    }
}
