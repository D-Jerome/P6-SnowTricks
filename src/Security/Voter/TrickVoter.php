<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Trick;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TrickVoter extends Voter
{
    public const AUTH = 'TRICK_AUTH';
    public const DELETE = 'TRICK_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!\in_array($attribute, [self::AUTH, self::DELETE], true)) {
            return false;
        }

        if (!$subject instanceof Trick) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if (false === $user->isActive()) {
            return false;
        }

        /** @var Trick $trick */
        $trick = $subject;

        return match($attribute) {
            self::AUTH   => $this->canAuth($trick, $user),
            self::DELETE => $this->canDelete($trick, $user),
            default      => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canDelete(Trick $trick, User $user): bool
    {
        return $user === $trick->getUser();
    }

    private function canAuth(Trick $trick, User $user): bool
    {
        return $user === $trick->getUser();
    }
}
