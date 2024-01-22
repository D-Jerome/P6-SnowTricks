<?php

declare(strict_types=1);

namespace App\Entity;

enum TypeMedia: string
{
    case Image = 'Image';
    case Video = 'Video';
}
