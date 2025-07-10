<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);

        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager): void
    {
        // Création d'un utilisateur admin
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setUsername('admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword(
            $this->passwordHasher->hashPassword($admin, 'pass123')
        );
        $manager->persist($admin);

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
        }
    }
}
