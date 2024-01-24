<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ValidationRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ValidationRepository::class)]
class Validation
{
    public const EXPIRATION_TIME = 10800;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $token = null;

    #[ORM\Column(nullable: false)]
    private \DateTime $createdAt;

    #[ORM\OneToOne(inversedBy: 'validation', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    private DateTime $expiredAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function isValid(): bool
    {
        $this->expiredAt = $this->getCreatedAt()->modify('+ 10800 second');

        $now = new DateTime();
        if ($now > $this->expiredAt) {
            return false;
        }

        return true;
    }
}
