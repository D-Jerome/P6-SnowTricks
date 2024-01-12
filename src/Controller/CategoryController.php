<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_categories')]
    public function showCategories(CategoryRepository $category): Response
    {
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'categories'      => $category->findAll(),
        ]);
    }

    #[Route('/category/{id<(\d+)>}', name: 'app_category')]
    public function showCategory(int $id, CategoryRepository $category): Response
    {
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'category'        => $category,
        ]);
    }

    #[Route('/category/add', name: 'app_category_add')]
    #[Route('/category/{id<(\d+)>}/edit', name: 'app_category_edit')]
    public function form(Category $category = null, Request $request, EntityManagerInterface $manager): Response
    {
        if (!$category) {
            $category = new Category();
        }

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($category);
            $manager->flush();

            return $this->redirectToRoute('app_categories');
        }

        return $this->render('category/form.html.twig', [
            'formCategory'            => $form->createView(),
            'category'                => $category,
        ]);
    }
}