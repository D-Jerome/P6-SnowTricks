<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\UpdatePasswordType;
use App\Form\UserAvatarType;
use App\Form\UserProfileType;
use App\Service\FileUploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserProfileController extends AbstractController
{
    #[Route('/user/', name: 'app_user_profile')]
    public function showProfile(Request $request, EntityManagerInterface $manager, FileUploaderService $fileUploader, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = $this->getUser();

        $formProfile = $this->createForm(UserProfileType::class, $user);
        $formPassword = $this->createForm(UpdatePasswordType::class, $user);
        $formAvatar = $this->createForm(UserAvatarType::class);

        $formProfile->handleRequest($request);
        $formPassword->handleRequest($request);
        $formAvatar->handleRequest($request);

        if ($formProfile->getClickedButton() && 'modifyProfile' === $formProfile->getClickedButton()->getName()) {
            if ($formProfile->isSubmitted()) {
                $manager->persist($user);
                $manager->flush();
            }
        }

        if ($formPassword->getClickedButton() && 'modifyPassword' === $formPassword->getClickedButton()->getName()) {
            if ($formPassword->isSubmitted() &&  $formPassword->get('password') ===  $formPassword->get('confirmPassword')) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $formPassword->get('password')->getData()
                    )
                );
                $manager->persist($user);
                $manager->flush();
            }
        }
        if ($formAvatar->getClickedButton() && 'modifyAvatar' === $formAvatar->getClickedButton()->getName()) {
            if ($formAvatar->isSubmitted()) {
                $avatarFile = $formAvatar->get('avatar')->getData();

                if (!empty($avatarFile)) {
                    /** @var UploadedFile $fileUploader */
                    $avatarFileName = $fileUploader->upload($avatarFile);
                    $user->setAvatar($avatarFileName);
                }
                $manager->persist($user);
                $manager->flush();
            }
        }

        return $this->render('user_profile/show.html.twig', [
            'user'         => $user,
            'formProfile'  => $formProfile->createView(),
            'formPassword' => $formPassword->createView(),
            'formAvatar'   => $formAvatar->createView(),
        ]);
    }
}
