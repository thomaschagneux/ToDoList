<?php

namespace App\Security;

use App\Entity\Task;
use App\Entity\User;

/**
 * @extends AbstractVoter<Task>
 */
class TaskVoter extends AbstractVoter
{
    public const TASK_EDIT = 'TASK_EDIT';
    public const TASK_DELETE = 'TASK_DELETE';
    public const TASK_CREATE = 'TASK_CREATE';
    public const TASK_VIEW = 'TASK_VIEW';
    public const TASK_LIST = 'TASK_LIST';

    private const ATTRIBUTES = [
        self::TASK_EDIT,
        self::TASK_DELETE,
        self::TASK_VIEW,
        self::TASK_CREATE,
        self::TASK_LIST,
    ];

    private const NO_SUBJECT_NEEDED = [
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
