<?php

namespace App\Security;

use App\Entity\Task;
use App\Entity\User;

class TaskVoter extends AbstractVoter
{
    public const string TASK_EDIT = 'TASK_EDIT';
    public const string TASK_DELETE = 'TASK_DELETE';
    public const string TASK_CREATE = 'TASK_CREATE';
    public const string TASK_VIEW = 'TASK_VIEW';
    public const string TASK_LIST = 'TASK_LIST';

    private const array ATTRIBUTES = [
        self::TASK_EDIT,
        self::TASK_DELETE,
        self::TASK_VIEW,
        self::TASK_CREATE,
        self::TASK_LIST,
    ];

    private const array NO_SUBJECT_NEEDED = [
        self::TASK_LIST,
        self::TASK_CREATE,
    ];

    public function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, self::ATTRIBUTES, true)) {
            return false;
        }

        if (in_array($attribute, self::NO_SUBJECT_NEEDED, true)) {
            return true;
        }

        return $subject instanceof Task;
    }

    protected function voteOnAttributeForUser(string $attribute, mixed $subject, User $user): bool
    {
        return match ($attribute) {
            self::TASK_EDIT,
            self::TASK_VIEW,
            self::TASK_DELETE => $this->ownerOnly($user, $subject),
            self::TASK_CREATE,
            self::TASK_LIST => $this->allowAll(),
            default => $this->denyAll(),
        };
    }
}
