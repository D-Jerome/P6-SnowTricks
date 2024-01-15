<?php

declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\Category;
use App\Entity\Media;
use App\Service\ThumbnailService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

#[AsEntityListener(event: Events::prePersist, entity: Category::class)]
#[AsEntityListener(event: Events::preUpdate, entity: Category::class)]
class MediaEntityListener
{
    public function __construct(
        private ThumbnailService $thumbnail,
    ) {
    }

    public function prePersist(Media $media, LifecycleEventArgs $event): void
    {
        $media->add();
    }

    public function preUpdate(Media $media, LifecycleEventArgs $event): void
    {
        $media->mo();
    }
}
