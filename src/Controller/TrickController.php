<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Media;
use App\Entity\Trick;
use App\Entity\TypeMedia;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use App\Service\FileUploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    #[Route('/trick/{slug<((\w+)-){0,}(\w+)>}/show', name: 'app_trick')]
    public function showTrick(string $slug, Trick $trick, TrickRepository $trickRepository, Request $request, EntityManagerInterface $manager): Response
    {
        $comments = $trick->getComments();
        $medias = $trick->getMedias();
        $mainPicture = null;
        foreach ($medias as $media) {
            if (TypeMedia::Image === $media->getTypeMedia()) {
                $mainPicture = $media->getPath();

                break;
            }
        }
        if (!$mainPicture) {
            $mainPicture = 'default/MainPics.jpg';
        }
        $countMedia = \count($medias);

        foreach ($comments as $comment) {
            $user = $manager->getRepository(User::class)->find($comment->getUser());
        }
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setTrick($trick);
            $comment->setUser($trick->getUser());
            $manager->persist($comment);

            $manager->flush();

            return $this->redirectToRoute('app_trick', ['slug' => $trick->getSlug()]);
        }

        return $this->render('trick/index.html.twig', [
            'trick'            => $trickRepository->findOneBy(['slug' => $slug]),
            'comments'         => $comments,
            'medias'           => $medias,
            'formComment'      => $form->createView(),
            'mainPicture'      => $mainPicture,
        ]);
    }

    #[Route('/trick/{slug<((\w+)-){0,}(\w+)>}/edit', name: 'app_trick_edit')]
    #[Route('/trick/add', name: 'app_trick_add')]
    public function form(Trick $trick = null, Request $request, EntityManagerInterface $manager, FileUploaderService $fileUploaderService): Response
    {
        $this->denyAccessUnlessGranted('TRICK_ADD', $trick);
        $this->denyAccessUnlessGranted('TRICK_EDIT', $trick);

        if (!$trick) {
            $trick = new Trick();
            /**
             * @var User $user
             */
            $user = $this->getUser();
            $trick->setUser($user);
        } else {
            $this->denyAccessUnlessGranted('TRICK_EDIT', $trick);
        }

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($trick->getMedias() as $key => $media) {
                if($media->getFile()) {
                    $uploadFileName = $fileUploaderService->upload($media->getFile(), '');
                    $media->setDescription($media->getFile()->getClientOriginalName());
                    $media->setPath($uploadFileName);
                    $manager->persist($media);
                }
            }

            $manager->persist($trick);
            $manager->flush();

            return $this->redirectToRoute('app_trick', ['slug' => $trick->getSlug()]);
        }
        // add Media

        return $this->render('trick/form.html.twig', [
            'formTrick'        => $form->createView(),
            'trick'            => $trick,
        ]);
    }

    #[Route('/trick/{slug<((\w+)-){0,}(\w+)>}/delete', name: 'app_trick_delete')]
    public function deleteTrick(Trick $trick, EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted('TRICK_DELETE', $trick);
        $manager->remove($trick);
        $manager->flush();

        $this->addflash(
            'success',
            "Le trick {$trick->getName()} a été supprimé avec succès !"
        );

        return $this->redirectToRoute('app_home');
    }
}
