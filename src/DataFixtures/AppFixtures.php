<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Random\RandomException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    /**
     * @throws RandomException
     */
    public function load(ObjectManager $manager): void
    {
        $users = $this->loadUsers($manager);
        $tasks = $this->loadTasks($manager);

        $manager->flush();
    }

    /**
     * @return array<User>
     */
    private function loadUsers(ObjectManager $manager): array
    {
        $users = [];
        // Création d'un utilisateur admin
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setUsername('admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword(
            $this->passwordHasher->hashPassword($admin, 'pass123')
        );
        $manager->persist($admin);

        $users[] = $admin;

        // Création d'utilisateurs standards
        $usersData = [
            [
                'email' => 'user1@example.com',
                'username' => 'user1',
                'password' => 'pass123',
                'roles' => ['ROLE_USER'],
            ],
            [
                'email' => 'user2@example.com',
                'username' => 'user2',
                'password' => 'pass123',
                'roles' => ['ROLE_USER'],
            ],
            [
                'email' => 'user3@example.com',
                'username' => 'user3',
                'password' => 'pass123',
                'roles' => ['ROLE_USER'],
            ],
        ];

        foreach ($usersData as $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setUsername($userData['username']);
            $user->setRoles($userData['roles']);
            $user->setPassword(
                $this->passwordHasher->hashPassword($user, $userData['password'])
            );
            $manager->persist($user);
            $users[] = $user;
        }

        // Création d'utilisateurs avec des données plus variées
        for ($i = 4; $i <= 10; ++$i) {
            $user = new User();
            $user->setEmail("user{$i}@example.com");
            $user->setUsername("user{$i}");
            $user->setRoles(['ROLE_USER']);
            $user->setPassword(
                $this->passwordHasher->hashPassword($user, 'pass123')
            );
            $manager->persist($user);
            $users[] = $user;
        }

        return $users;
    }

    /**
     * @throws RandomException
     */
    private function loadTasks(ObjectManager $manager): array
    {
        $tasks = [];
        for ($i = 0; $i < 20; ++$i) {
            $randomBool = random_int(0, 100);
            $task = new Task();
            $task
                ->setTitle("task{$i}")
                ->setContent("task{$i}")
                ->setIsDone(50 > $randomBool);

            $manager->persist($task);
            $tasks[] = $task;
        }

        return $tasks;
    }
}
