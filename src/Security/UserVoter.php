<?php

namespace App\Security;

use App\Entity\User;

class UserVoter extends AbstractVoter
{
    public const string USER_EDIT = 'USER_EDIT';
    public const string USER_DELETE = 'USER_DELETE';
    public const string USER_CREATE = 'USER_CREATE';
    public const string USER_VIEW = 'USER_VIEW';
    public const string USER_LIST = 'USER_LIST';

    private const array ATTRIBUTES = [
        self::USER_EDIT,
        self::USER_DELETE,
        self::USER_VIEW,
        self::USER_CREATE,
        self::USER_LIST,
    ];

    private const array NO_SUBJECT_NEEDED = [
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
