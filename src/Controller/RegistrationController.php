<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Entity\Validation;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/signup', name: 'app_security_signup')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager, SendMailService $mail): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $validation = new Validation();
            $validation->setUser($user);
            $token = (string) md5(uniqid());
            $validation->setToken($token);
            $validation->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($validation);
            $entityManager->flush();

            $mail->send(
                'no-reply@snowTricks.comm',
                $user->getEmail(),
                'Lien d\'Activation de votre compte sur le site communautaire SnowTricks',
                'register',
                compact('user', 'token')
            );

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/signup.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/check/{token}', name: 'app_user_check')]
    public function verifyUser(string $token, ValidationRepository $validationRepository, UserRepository $usersRepository, EntityManagerInterface $em): Response
    {
        $valid = $validationRepository->findOneBy(['token'=>$token]);

        $user = $valid->getUser();
        if(null === $valid || false === $valid->isValid() || true === $user->getEnable() || null !== $user->getResetToken()) {
            $this->addFlash('danger', 'Le token est invalide ou a expiré');

            return $this->redirectToRoute('app_home');
        }

        $user->setActive(true);
        $em->persist($user);
        $em->flush();
        $this->addFlash('success', 'Votre compte est activé. Vous pouvez vous connecter');

        return $this->redirectToRoute('app_login');
    }

    #[Route('/resendmail', name: 'app_user_resend_mail')]
    public function resendMail(Request $request, SendMailService $mail, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $user = $this->getUser();

        if(!$user) {
            $this->addFlash('danger', 'Vous devez être connecté pour accéder à cette page');

            return $this->redirectToRoute('app_login');
        }

        if($user->isActive()) {
            $this->addFlash('warning', 'Cet utilisateur est déjà activé');

            return $this->redirectToRoute('profile_index');
        }

        $validation = $user->getValidation();
        $validation->setUser($user);
        $validation->setToken(md5(uniqid()));
        $validation->setCreatedAt(new \DateTimeImmutable());

        $entityManager->persist($validation);
        $entityManager->flush();

        $mail->send(
            'no-reply@snowTricks.comm',
            $user->getEmail(),
            'Lien d\'Activation de votre compte sur le site communautaire SnowTricks',
            'register',
            $validation->getToken()
        );

        $this->addFlash('success', 'Email de vérification envoyé');

        return $this->redirectToRoute('profile_index');
    }
}
