<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $path = null;

    #[ORM\Column(enumType: TypeMedia::class)]
    #[Assert\NotNull()]
    private ?TypeMedia $typeMedia = null;

    #[ORM\ManyToOne(inversedBy: 'medias')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull()]
    private ?Trick $trick = null;

    #[Assert\File(
        maxSize: 2048000,
        maxSizeMessage: 'La taille maximale ne doit pas dépassée',
        mimeTypes: ['image/*'],
        mimeTypesMessage: 'Merci de sélectionner une Image Valide au format(jpg, jpeg, webp, png)',
    )]
    #[Assert\Image(
        maxWidth: 1920,
        maxWidthMessage: 'Les dimensions de l\'image ne doivent pas dépassée 1920*1080',
        allowPortrait: false,
        allowPortraitMessage: 'Uniquement des images au format paysage',
        detectCorrupted: true,
        maxHeight: 1080,
        maxHeightMessage: 'Les dimensions de l\'image ne doivent pas dépassée 1920*1080',
    )]
    private ?UploadedFile $file = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function getTypeMedia(): ?TypeMedia
    {
        return $this->typeMedia;
    }

    public function setTypeMedia(TypeMedia $typeMedia): static
    {
        $this->typeMedia = $typeMedia;

        return $this;
    }

    public function getTrick(): ?Trick
    {
        return $this->trick;
    }

    public function setTrick(?Trick $trick): static
    {
        $this->trick = $trick;

        return $this;
    }

    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file = null): static
    {
        $this->file = $file;

        return $this;
    }
}
