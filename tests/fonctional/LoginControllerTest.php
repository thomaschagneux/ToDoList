<?php

namespace Tests\fonctional;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginControllerTest extends AbstractWebTestCase
{
    private User $testUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testUser = $this->createTestUser();
    }

    private function createTestUser(): User
    {
        $container = static::getContainer();

        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = $container->get('security.user_password_hasher');

        $user = new User();
        $user->setEmail('test@example.com');
        $user->setUsername('test@example.com'); // Adaptez selon votre logique
        $user->setPassword($passwordHasher->hashPassword($user, 'password'));
        $user->setRoles(['ROLE_USER']);

        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    public function testLoginPageIsAccessible(): void
    {
        $this->client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
        $this->assertSelectorExists('input[name="_username"]');
        $this->assertSelectorExists('input[name="_password"]');
    }

    public function testLoginWithInvalidEmail(): void
    {
        $this->client->request('GET', '/login');

        $this->client->submitForm('Sign in', [
            '_username' => 'nonexistent@example.com',
            '_password' => 'password',
        ]);

        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();

        $this->assertSelectorTextContains('.alert-danger', 'Invalid credentials.');
    }

    public function testLoginWithInvalidPassword(): void
    {
        $this->client->request('GET', '/login');

        $this->client->submitForm('Sign in', [
            '_username' => $this->testUser->getEmail(),
            '_password' => 'wrong-password',
        ]);

        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();

        // Vérifier que l'erreur ne révèle pas que l'utilisateur existe
        $this->assertSelectorTextContains('.alert-danger', 'Invalid credentials.');
    }

    public function testSuccessfulLogin(): void
    {
        $this->client->request('GET', '/login');

        $this->client->submitForm('Sign in', [
            '_username' => $this->testUser->getEmail(),
            '_password' => 'password',
        ]);

        $this->assertResponseRedirects('/');
        $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorNotExists('.alert-danger');
    }

    public function testAlreadyLoggedInUserIsRedirected(): void
    {
        $this->client->loginUser($this->testUser);

        $this->client->request('GET', '/login');

        $this->assertResponseRedirects('/');
    }

    public function testLogoutRoute(): void
    {
        $this->client->loginUser($this->testUser);

        $this->client->request('GET', '/logout');

        $this->assertResponseRedirects();
    }

    public function testLoginFormPersistsUsernameOnError(): void
    {
        $this->client->request('GET', '/login');

        $this->client->submitForm('Sign in', [
            '_username' => 'test@example.com',
            '_password' => 'wrong-password',
        ]);

        $this->client->followRedirect();

        $this->assertSelectorExists('input[name="_username"]');

        $usernameInput = $this->client->getCrawler()->filter('input[name="_username"]');
        $this->assertEquals('test@example.com', $usernameInput->attr('value'), 'The username should persist on error.');
    }
}
