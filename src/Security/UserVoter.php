<?php

namespace App\Security;

use App\Entity\User;

/**
 * @extends AbstractVoter<User>
 */
class UserVoter extends AbstractVoter
{
    public const USER_EDIT = 'USER_EDIT';
    public const USER_DELETE = 'USER_DELETE';
    public const USER_CREATE = 'USER_CREATE';
    public const USER_VIEW = 'USER_VIEW';
    public const USER_LIST = 'USER_LIST';

    private const ATTRIBUTES = [
        self::USER_EDIT,
        self::USER_DELETE,
        self::USER_VIEW,
        self::USER_CREATE,
        self::USER_LIST,
    ];

    private const NO_SUBJECT_NEEDED = [
        self::USER_LIST,
        self::USER_CREATE,
    ];

    public function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, self::ATTRIBUTES, true)) {
            return false;
        }

        if (in_array($attribute, self::NO_SUBJECT_NEEDED, true)) {
            return true;
        }

        return $subject instanceof User;
    }

    protected function voteOnAttributeForUser(string $attribute, mixed $subject, User $user): bool
    {
        return match ($attribute) {
            self::USER_EDIT,
            self::USER_VIEW,
            self::USER_DELETE => $this->ownerOnly($user, $subject),
            self::USER_CREATE,
            self::USER_LIST => $this->allowAll(),
            default => $this->denyAll(),
        };
    }
}
