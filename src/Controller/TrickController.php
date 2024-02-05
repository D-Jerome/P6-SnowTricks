<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\TypeMedia;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\TrickType;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use App\Service\FileUploaderService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Webmozart\Assert\Assert;

class TrickController extends AbstractController
{
    #[Route('/trick/{slug<((\w+)-){0,}(\w+)>}/show', name: 'app_trick')]
    public function showTrick(string $slug, Trick $trick, TrickRepository $trickRepository, CommentRepository $commentRepository, Request $request, EntityManagerInterface $manager): Response
    {
        $comments = $trick->getComments();
        $offset = max(0, $request->query->getInt('offset', 0));
        $maxOffset = \count($commentRepository->findAll());
        if ($offset > $maxOffset) {
            $offset = (int) (ceil($maxOffset / CommentRepository::PAGINATOR_PER_PAGE) - 1) * CommentRepository::PAGINATOR_PER_PAGE;
        }
        $pagedComments = $commentRepository->getCommentPaginator($trick, $offset);

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
        // $countMedia = \count($medias);

        // foreach ($comments as $comment) {
        //     $user = $manager->getRepository(User::class)->find($comment->getUser());
        // }
       
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            Assert::isInstanceOf($user, User::class);
            
            $comment->setTrick($trick);
            $comment->setUser($user);
            $manager->persist($comment);

            $manager->flush();

            $this->addFlash(
                'success',
                'Votre commentaire a bien été enregistré !'
            );

            return $this->redirectToRoute('app_trick', ['slug' => $trick->getSlug()]);
        }

        return $this->render('trick/index.html.twig', [
            'trick'              => $trickRepository->findOneBy(['slug' => $slug]),
            'comments'           => $pagedComments,
            'medias'             => $medias,
            'formComment'        => $form->createView(),
            'mainPicture'        => $mainPicture,
            'offset'             => $offset,
            'previous'           => $offset - CommentRepository::PAGINATOR_PER_PAGE,
            'next'               => min(\count($pagedComments), $offset + CommentRepository::PAGINATOR_PER_PAGE),
            'maxOffset'          => $maxOffset,
            'TypeMediaEnumImage' => TypeMedia::Image,
        ]);
    }

    #[Route('/trick/{slug<((\w+)-){0,}(\w+)>}/edit', name: 'app_trick_edit')]
    #[Route('/trick/add', name: 'app_trick_add')]
    public function form(Trick $trick = null, Request $request, EntityManagerInterface $manager, FileUploaderService $fileUploaderService, ValidatorInterface $validator): Response
    {
        if (!$trick) {
            $trick = new Trick();
            /**
             * @var User $user
             */
            $user = $this->getUser();
            $trick->setUser($user);
        }

        $this->denyAccessUnlessGranted('TRICK_AUTH', $trick);
        $form = $this->createForm(TrickType::class, $trick);
        try {
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                foreach ($form->getErrors() as $error) {
                    Assert::isInstanceOf($error, FormError::class);
                    $this->addFlash('danger', $error->getMessage());
                }

                if($form->isValid()) {
                    if ($trick->getId()) {
                        $trick->setUpdatedAt(new DateTimeImmutable());
                    }

                    foreach ($trick->getMedias() as $key => $media) {
                        if(!$media->getId() && (!$media->getFile()) && !$media->getPath()) {
                            $trick->removeMedia($media);
                        } else {
                            if (TypeMedia::Image === $media->getTypeMedia()) {
                                if($media->getFile()) {
                                    $uploadFileName = $fileUploaderService->upload($media->getFile(), '');
                                    if (!$uploadFileName) {
                                        $this->addFlash('danger', 'Un problème est survenu....');

                                        return $this->redirectToRoute('app_trick_edit', ['slug' => $trick->getSlug()]);
                                    }
                                    $media->setDescription($media->getFile()->getClientOriginalName());
                                    $media->setPath($uploadFileName);
                                    $manager->persist($media);
                                }
                            } else {
                                Assert::notNull($media->getPath());

                                if(preg_match_all('/<iframe[^>]*>(?:.*?)<\/iframe>/', $media->getPath(), $matches)) {
                                    $path = (string) str_replace('autoplay=1', '', $matches[0][0]);
                                    $path = str_replace('position:absolute;', '', $path);
                                    $expPath = explode(' ', $path);
                                    foreach($expPath as $k => $detail) {
                                        if ('class=' === substr($detail, 0, 6)) {
                                            $expPath[$k] = '';
                                        }
                                        if (('width=' === substr($detail, 0, 6)) || ('height=' === substr($detail, 0, 7))) {
                                            $expPath[$k] = '';
                                        }
                                    }
                                    $path = implode(' ', $expPath);
                                    $path = str_replace('<iframe ', '<iframe class="responsive-iframe" width="500px" height="280px"', $path);
                                    $media->setDescription('video');
                                    $media->setPath($path);
                                    $manager->persist($media);
                                } else {
                                    $this->addFlash('danger', 'le lien de la video ne correspond pas a un lien integré (embed)');

                                    return $this->redirectToRoute('app_trick_edit', ['slug' => $trick->getSlug()]);
                                }
                            }
                        }
                    }

                    $manager->persist($trick);
                    $manager->flush();

                    $this->addFlash(
                        'success',
                        'Le trick <strong>'.$trick->getName().'</strong> a bien été enregistré !'
                    );

                    return $this->redirectToRoute('app_trick', ['slug' => $trick->getSlug()]);
                }
            }

            return $this->render('trick/form.html.twig', [
                'formTrick'        => $form->createView(),
                'trick'            => $trick,
            ]);
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Une erreur s\'est produite....');

            return $this->render('trick/form.html.twig', [
                'formTrick'        => $form->createView(),
                'trick'            => $trick,
            ]);
        }
    }

    #[Route('/trick/{slug<((\w+)-){0,}(\w+)>}/delete', name: 'app_trick_delete')]
    public function deleteTrick(Trick $trick, EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted('TRICK_DELETE', $trick);
        $fileSystem = new Filesystem();
        foreach ($trick->getMedias() as $media) {
            if (Typemedia::Image === $media->getTypeMedia()) {
                $fileSystem->remove('uploads/'.$media->getPath());
            }
        }

        $manager->remove($trick);
        $manager->flush();

        $this->addflash(
            'success',
            "Le trick {$trick->getName()} a été supprimé avec succès !"
        );

        return $this->redirectToRoute('app_home');
    }
}
