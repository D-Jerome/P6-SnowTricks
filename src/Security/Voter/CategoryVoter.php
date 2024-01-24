<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Category;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @template TAttribute
 * @extends  Voter<string>
 * @template TSubject
 * @extends  Voter<mixed>
 */
class CategoryVoter extends Voter
{

    public const ADD = 'CATEGORY_ADD';
    public const EDIT = 'CATEGORY_EDIT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!\in_array($attribute, [self::ADD, self::EDIT], true)) {
            return false;
        }

        // only vote on `Post` objects
        if (!$subject instanceof Category) {
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

        
        /** @var Category $category */
        $category = $subject;

        return match($attribute) {
            self::EDIT   => $this->canEdit($category, $user),
            self::ADD    => $this->canAdd($category, $user),
            default      => throw new \LogicException('This code should not be reached!')
        };
    }

    
    private function canEdit(Category $category): bool
    {
        return true;
    }

    private function canAdd(Category $category): bool
    {
        return true;
    }
}
