<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\UpdatePasswordType;
use App\Form\UserAvatarType;
use App\Form\UserProfileType;
use App\Service\FileUploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Webmozart\Assert\Assert;

class UserProfileController extends AbstractController
{
    #[Route('/user/', name: 'app_user_profile')]
    public function showProfile(Request $request, EntityManagerInterface $manager, FileUploaderService $fileUploader, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        /**
         * @var User $user
         */
        $user = $this->getUser();
        /**
         * @var FormInterface $formProfile
         */
        $formProfile = $this->createForm(UserProfileType::class);

        $formPassword = $this->createForm(UpdatePasswordType::class);

        $formAvatar = $this->createForm(UserAvatarType::class);

        $formProfile->get('username')->setData($user->getUsername());
        $formProfile->get('email')->setData($user->getEmail());

        $formProfile->handleRequest($request);

        if ($formProfile->isSubmitted() && $formProfile->isValid()) {
            Assert::string($formProfile->get('username')->getData());
            if ($user->getUsername() !== $formProfile->get('username')->getData()) {
                $user->setUsername($formProfile->get('username')->getData());
            }
            Assert::string($formProfile->get('email')->getData());
            if ($user->getEmail() !== $formProfile->get('email')->getData()) {
                $user->setEmail($formProfile->get('email')->getData());
            }
            $manager->persist($user);
            $manager->flush();
        }

        $formPassword->handleRequest($request);

        if ($formPassword->isSubmitted() && $formPassword->isValid()) {
            Assert::String($formPassword->get('password')->getData());

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $formPassword->get('password')->getData()
                )
            );
            $manager->persist($user);
            $manager->flush();
        }
        $formAvatar->handleRequest($request);
        if ($formAvatar->isSubmitted() && $formAvatar->isValid()) {
            /**
             * @var UploadedFile $avatarFile
             */
            $avatarFile = $formAvatar->get('avatar')->getData();

            $avatarFileName = $fileUploader->upload($avatarFile, '');
            $user->setAvatar($avatarFileName);

            $manager->persist($user);
            $manager->flush();
        }

        return $this->render('user_profile/show.html.twig', [
            'user'         => $user,
            'formProfile'  => $formProfile->createView(),
            'formPassword' => $formPassword->createView(),
            'formAvatar'   => $formAvatar->createView(),
        ]);
    }

    #[Route('/usertest', name: 'app_user_test')]
    public function show(Request $request, EntityManagerInterface $manager, FileUploaderService $fileUploader): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        $formAvatar = $this->createForm(UserAvatarType::class);

        $formAvatar->handleRequest($request);
        if ($formAvatar->isSubmitted() && $formAvatar->isValid()) {
            /**
             * @var UploadedFile $avatarFile
             */
            $avatarFile = $formAvatar->get('avatar')->getData();

            $avatarFileName = $fileUploader->upload($avatarFile, '');
            $user->setAvatar($avatarFileName);

            $manager->persist($user);
            $manager->flush();
        }

        return $this->render('user_profile/test.html.twig', [
            'user'         => $user,
            'formAvatar'   => $formAvatar->createView(),
        ]);
    }
}
