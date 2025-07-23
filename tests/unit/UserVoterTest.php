<?php

namespace Tests\unit;

use App\Entity\User;
use App\Security\UserVoter;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class UserVoterTest extends TestCase
{
    private Security $security;
    private UserVoter $userVoter;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->security = $this->createMock(Security::class);
        $this->userVoter = new UserVoter($this->security);
    }

    /**
     * @throws Exception
     */
    public function testSupportsWithValidAttributes(): void
    {
        $user = $this->createMock(User::class);

        $this->assertTrue($this->userVoter->supports(UserVoter::USER_EDIT, $user));
        $this->assertTrue($this->userVoter->supports(UserVoter::USER_DELETE, $user));
        $this->assertTrue($this->userVoter->supports(UserVoter::USER_VIEW, $user));
        $this->assertTrue($this->userVoter->supports(UserVoter::USER_CREATE, null));
        $this->assertTrue($this->userVoter->supports(UserVoter::USER_LIST, null));
    }

    /**
     * @throws Exception
     */
    public function testVoteGrantedIfAdmin(): void
    {
        $adminUser = $this->createMock(User::class);
        $targetUser = $this->createMock(User::class);

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($adminUser);

        // Simule un administrateur
        $this->security->method('isGranted')->with('ROLE_ADMIN')->willReturn(true);

        $vote = $this->userVoter->vote($token, $targetUser, [UserVoter::USER_EDIT]);

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $vote);
    }

    /**
     * @throws Exception
     */
    public function testVoteGrantedIfOwner(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setUsername('owner');

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        // Simule que l'utilisateur n'est pas admin
        $this->security->method('isGranted')->with('ROLE_ADMIN')->willReturn(false);

        $vote = $this->userVoter->vote($token, $user, [UserVoter::USER_VIEW]);

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $vote);
    }

    /**
     * @throws Exception
     */
    public function testVoteDeniedIfNotOwner(): void
    {
        $user1 = new User();
        $user1->setEmail('user1@example.com');
        $user1->setUsername('user1');

        $user2 = new User();
        $user2->setEmail('user2@example.com');
        $user2->setUsername('user2');

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user1);

        $this->security->method('isGranted')->with('ROLE_ADMIN')->willReturn(false);

        $vote = $this->userVoter->vote($token, $user2, [UserVoter::USER_EDIT]);

        $this->assertSame(VoterInterface::ACCESS_DENIED, $vote);
    }

    /**
     * @throws Exception
     */
    public function testVoteDeniedIfNotLoggedIn(): void
    {
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn(null); // pas connecté

        $vote = $this->userVoter->vote($token, null, [UserVoter::USER_CREATE]);

        $this->assertSame(VoterInterface::ACCESS_DENIED, $vote);
    }
}
