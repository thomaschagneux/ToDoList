<?php

namespace Tests\fonctional;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class AbstractWebTestCase extends WebTestCase
{
    protected KernelBrowser $client;

    protected EntityManagerInterface $entityManager;

    protected UserPasswordHasherInterface $userPasswordHasher;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $kernel = $this->client->getKernel();

        $application = new Application($kernel);
        $application->setAutoExit(false);

        $output = new BufferedOutput();

        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);

        $this->userPasswordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);

        $application->run(new ArrayInput([
            'command' => 'doctrine:database:drop',
            '--if-exists' => true,
            '--force' => true,
            '--env' => 'test',
        ]), $output);

        $application->run(new ArrayInput([
            'command' => 'doctrine:database:create',
            '--env' => 'test',
        ]), $output);

        $application->run(new ArrayInput([
            'command' => 'doctrine:migrations:migrate',
            '--no-interaction' => true,
            '--env' => 'test',
        ]), $output);

        $application->run(new ArrayInput([
            'command' => 'doctrine:fixtures:load',
            '--no-interaction' => true,
            '--env' => 'test',
        ]), $output);
    }

    protected function getEntityManager(): ObjectManager
    {
        /** @var ManagerRegistry $registry */
        $registry = static::getContainer()->get('doctrine');

        $em = $registry->getManager();

        if (!$em->isOpen()) {
            $em = $registry->resetManager();
        }

        return $em;
    }

    protected function createUser(string $email = 'test@example.com'): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setPassword('fake');
        $user->setRoles(['ROLE_USER']);

        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    protected function createUserWithRole(array $role): User
    {
        $user = new User();
        $user->setEmail('user@mail.com');
        $user->setRoles($role);
        $user->setUsername('user');
        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'pass123'));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
