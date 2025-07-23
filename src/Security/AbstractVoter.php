<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @template TSubject
 *
 * @extends Voter<string, TSubject>
 */
abstract class AbstractVoter extends Voter
{
    public function __construct(
        private readonly Security $security,
    ) {
    }

    abstract protected function supports(string $attribute, mixed $subject): bool;

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        // Les administrateurs peuvent tout faire
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return $this->voteOnAttributeForUser($attribute, $subject, $user);
    }

    abstract protected function voteOnAttributeForUser(string $attribute, mixed $subject, User $user): bool;

    protected function isOwner(User $user, mixed $subject): bool
    {
        if ($subject instanceof User) {
            return $subject === $user;
        }

        return is_object($subject)
            && method_exists($subject, 'getUser')
            && $subject->getUser() === $user;
    }

    protected function allowAll(): bool
    {
        return true;
    }

    protected function denyAll(): bool
    {
        return false;
    }

    protected function ownerOnly(User $user, mixed $subject): bool
    {
        return $this->isOwner($user, $subject);
    }
}
