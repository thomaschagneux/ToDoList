<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    public const USER_EDIT = 'USER_EDIT';
    public const USER_DELETE = 'USER_DELETE';
    public const USER_CREATE = 'USER_CREATE';
    public const USER_VIEW = 'USER_VIEW';

    public const USER_LIST = 'USER_LIST';

    public function __construct(
        private readonly Security $security,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [
            self::USER_EDIT,
            self::USER_DELETE,
            self::USER_VIEW,
            self::USER_CREATE,
            self::USER_LIST,
        ], true)) {
            return false;
        }

        if (in_array($attribute, [
            self::USER_LIST, self::USER_CREATE,
        ])) {
            return true;
        }

        return $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return match ($attribute) {
            self::USER_EDIT => $this->canEdit($user, $subject),
            self::USER_DELETE => $this->canDelete($user, $subject),
            self::USER_VIEW => $this->canView($user, $subject),
            self::USER_CREATE => $this->canCreate(),
            self::USER_LIST => $this->canList(),
            default => false,
        };
    }

    private function canEdit(User $user, User $targetUser): bool
    {
        if ($user === $targetUser) {
            return true;
        }

        return false;
    }

    private function canDelete(User $user, User $targetUser): bool
    {
        if ($user === $targetUser) {
            return true;
        }

        return false;
    }

    private function canView(User $user, User $targetUser): bool
    {
        if ($user === $targetUser) {
            return true;
        }

        return false;
    }

    private function canCreate(): bool
    {
        return true;
    }

    private function canList(): bool
    {
        return true;
    }
}
