<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Trick;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TrickVoter extends Voter
{
    public const EDIT = 'TRICK_EDIT';
    public const DELETE = 'TRICK_DELETE';
    public const ADD = 'TRICK_ADD';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!\in_array($attribute, [self::EDIT, self::DELETE, self::ADD], true)) {
            return false;
        }

        // only vote on `Post` objects
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

        if (!$user->isActive()) {
            return false;
        }

        /** @var Trick $trick */
        $trick = $subject;

        return match($attribute) {
            self::EDIT   => $this->canEdit($trick, $user),
            self::DELETE => $this->canDelete($trick, $user),
            self::ADD    => $this->canAdd($trick, $user),
            default      => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canDelete(Trick $trick, User $user): bool
    {
        return $user === $trick->getUser();
    }

    private function canEdit(Trick $trick, User $user): bool
    {
        return $user === $trick->getUser();
    }

    private function canAdd(Trick $trick, User $user): bool
    {
        return $user === $trick->getUser();
    }
}
