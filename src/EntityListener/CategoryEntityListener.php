<?php

declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsEntityListener(event: Events::prePersist, entity: Category::class)]
#[AsEntityListener(event: Events::preUpdate, entity: Category::class)]
class CategoryEntityListener
{
    public function __construct(
        private SluggerInterface $slugger,
    ) {
    }

    public function prePersist(Category $category): void
    {
        $category->computeSlug($this->slugger);
    }

    public function preUpdate(Category $category): void
    {
        $category->computeSlug($this->slugger);
    }
}
