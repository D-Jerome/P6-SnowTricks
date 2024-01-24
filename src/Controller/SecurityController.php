<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\EmailForgotPasswordType;
use App\Form\UpdatePasswordType;
use App\Repository\UserRepository;
use App\Repository\ValidationRepository;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Webmozart\Assert\Assert;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_security_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_security_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/forgot-password', name: 'app_security_forgot_password')]
    public function forgotPassword(Request $request, SendMailService $mail, UserRepository $userRepository, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(EmailForgotPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->findOneBy(['email' => $form->get('email')->getData()]);
            if ($user) {
                $validation = $user->getValidation();
                Assert::notNull($validation);
                $validation->setUser($user);
                $token = (string) (md5(uniqid()).md5(uniqid()));
                $validation->setToken($token);
                $validation->setCreatedAt(new \DateTime());

                $manager->persist($validation);
                $manager->flush();
                Assert::notNull($user->getEmail());
                $mail->send(
                    'no-reply@snowTricks.comm',
                    $user->getEmail(),
                    'Lien de changement de votre mot de passe sur le site communautaire SnowTricks',
                    'changePassword',
                    [
                        'user'  => $user,
                        'token' => $token,
                    ]
                );
            }
            $this->addFlash('info', 'Si vous avez un compte, un email a été envoyé sinon pensez à vous inscrire....');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('security/forgotPassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route(path:'/reset/{token}', name:'app_reset_password')]
    public function resetPassword(string $token , Request $request, UserPasswordHasherInterface $userPasswordHasher, ValidationRepository $validationRepository, EntityManagerInterface $manager): Response
    {
       
        $valid = $validationRepository->findOneBy(['token'=>$token]);
        if($valid) {
            /**
             * @var User $user
             */
            $user = $valid->getUser();
        }
        if(null === $valid || false === $valid->isValid()  ) {
            $this->addFlash('danger', 'Le token est invalide ou a expiré');

            return $this->redirectToRoute('app_home');
        }
        
        $form = $this->createForm(UpdatePasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                $manager->persist($user);
                $manager->flush();
                
                $this->addFlash('success', 'Mot de passe modifié avec succés');
                $this->addFlash('info', 'Veuillez vous connecter');
                return $this->redirectToRoute('app_security_login');

        }

        return $this->render('security/resetPassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
