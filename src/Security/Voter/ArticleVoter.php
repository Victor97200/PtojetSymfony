<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Article;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ArticleVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof Article && in_array($attribute, ['view', 'edit']);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        // Articles can be viewed by everyone
        if ('view' === $attribute) {
            return true;
        }


        $user = $token->getUser();
        $owner = $subject->getUser();


        if ('edit' === $attribute && ($owner instanceof User) && ($user instanceof User) && $user->getId() === $owner->getId()) {
            return true;
        }

        return false;
    }
}