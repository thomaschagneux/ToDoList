<?php

namespace Tests\unit;

use App\Entity\Task;
use App\Entity\User;
use App\Security\TaskVoter;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class TaskVoterTest extends TestCase
{
    private Security $security;
    private TaskVoter $taskVoter;

    protected function setUp(): void
    {
        $this->security = $this->createMock(Security::class);
        $this->taskVoter = new TaskVoter($this->security);
    }

    public function testSupports(): void
    {
        $task = new Task();

        $this->assertTrue($this->taskVoter->supports(TaskVoter::TASK_CREATE, null), 'TASK_CREATE should be supported without a Task subject');
        $this->assertTrue($this->taskVoter->supports(TaskVoter::TASK_EDIT, $task), 'TASK_EDIT should be supported with a Task subject');
        $this->assertTrue($this->taskVoter->supports(TaskVoter::TASK_DELETE, $task), 'TASK_DELETE should be supported with a Task subject');
        $this->assertTrue($this->taskVoter->supports(TaskVoter::TASK_VIEW, $task), 'TASK_VIEW should be supported with a Task subject');
        $this->assertTrue($this->taskVoter->supports(TaskVoter::TASK_LIST, null), 'TASK_LIST should be supported without a Task subject');
    }

    public function testVoteGrantedForAdmin(): void
    {
        $adminUser = new User();
        $task = new Task();

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($adminUser);

        $this->security->method('isGranted')->with('ROLE_ADMIN')->willReturn(true);

        $vote = $this->taskVoter->vote($token, $task, [TaskVoter::TASK_EDIT]);
        $this->assertSame(VoterInterface::ACCESS_GRANTED, $vote);
    }

    public function testVoteGrantedForOwner(): void
    {
        $owner = new User();
        $task = new Task();
        $task->setUser($owner);

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($owner);

        $this->security->method('isGranted')->with('ROLE_ADMIN')->willReturn(false);

        $vote = $this->taskVoter->vote($token, $task, [TaskVoter::TASK_VIEW]);
        $this->assertSame(VoterInterface::ACCESS_GRANTED, $vote);
    }

    /**
     * @throws Exception
     */
    public function testVoteDeniedForNonOwner(): void
    {
        $owner = new User();
        $stranger = new User();

        $task = new Task();
        $task->setUser($owner);

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($stranger);

        $this->security->method('isGranted')->with('ROLE_ADMIN')->willReturn(false);

        $vote = $this->taskVoter->vote($token, $task, [TaskVoter::TASK_DELETE]);
        $this->assertSame(VoterInterface::ACCESS_DENIED, $vote);
    }

    /**
     * @throws Exception
     */
    public function testVoteDeniedIfNotLoggedIn(): void
    {
        $task = new Task();

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn(null); // Non connecté

        $vote = $this->taskVoter->vote($token, $task, [TaskVoter::TASK_EDIT]);
        $this->assertSame(VoterInterface::ACCESS_DENIED, $vote);
    }
}
