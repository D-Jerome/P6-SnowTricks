<?php

declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsEntityListener(event: Events::prePersist, entity: Trick::class)]
#[AsEntityListener(event: Events::preUpdate, entity: Trick::class)]
class TrickEntityListener
{
    public function __construct(
        private SluggerInterface $slugger,
    ) {
    }

    public function prePersist(Trick $trick): void
    {
        $trick->computeSlug($this->slugger);
    }

    public function preUpdate(Trick $trick): void
    {
        $trick->computeSlug($this->slugger);
    }
}
